<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $horsetools_options;
# Code tự động lưu ảnh vào lưu trữ của bạn
class horsetools_save_images_hots {
    function __construct() {
        add_filter('content_save_pre', array($this, 'horsetools_post_save_images'));
    }
    function horsetools_get_remote_image_data($url) {
		// horsetools_safe_fetch() (inc/http.php) enforces http/https, runs
		// wp_http_validate_url(), then checks every address the host resolves
		// to against the blocked ranges — wp_http_validate_url() on its own
		// permits 169.254.169.254 (cloud instance metadata) and all IPv6
		// internal addresses. It also refuses redirects.
		$response = horsetools_safe_fetch($url);
		if (is_wp_error($response)) {
			return false;
		}
		if (wp_remote_retrieve_response_code($response) !== 200) {
			return false;
		}
		$type = wp_remote_retrieve_header($response, 'content-type');
		if (is_array($type)) {
			$type = reset($type);
		}
		$type = is_string($type) ? strtolower(trim($type)) : '';
		$data = wp_remote_retrieve_body($response);

		// Kiểm tra nếu dữ liệu hợp lệ và là hình ảnh
		if ($data && strpos($type, 'image/') === 0) {
			// Sử dụng getimagesizefromstring để kiểm tra xem dữ liệu có phải là ảnh không
			$image_info = getimagesizefromstring($data);
			if ($image_info === false) {
				return false;  // Dữ liệu không phải là hình ảnh hợp lệ
			}
			
			// Kiểm tra nếu là ảnh PNG
			if ($image_info[2] == IMAGETYPE_PNG) {
				// Kiểm tra sự tồn tại của hàm horsetools_png_8bit_to_32bit
				if (function_exists('horsetools_png_8bit_to_32bit')) {
					// Tạo ảnh tạm để xử lý
					$temp_file = tempnam(sys_get_temp_dir(), 'horsetools_image');
					file_put_contents($temp_file, $data);

					// Thực hiện chuyển đổi ảnh nếu cần
					$file_info = ['tmp_name' => $temp_file, 'type' => $type];
					$file_info = horsetools_png_8bit_to_32bit($file_info); 

					// Đọc lại dữ liệu ảnh đã chuyển đổi
					$image_data = file_get_contents($file_info['tmp_name']);
					
					// Xóa ảnh tạm sau khi sử dụng
					unlink($file_info['tmp_name']); 

					// Trả về dữ liệu ảnh đã chuyển đổi
					return $image_data;
				}
			}
			
			// Nếu không phải ảnh PNG hoặc không cần chuyển đổi, trả về dữ liệu gốc
			return $data;
		}

		// Nếu không phải hình ảnh hợp lệ, trả về false
		return false;
	}
    function horsetools_post_save_images($content) {
		global $post, $horsetools_options;
		if (!$post || !isset($post->ID)) return $content;
		$post_id = $post->ID;
		$post_status = get_post_status($post_id);
		if (!in_array($post_status, ['publish', 'draft', 'pending'])) return $content;
		// Chỉ người dùng có quyền tải file mới kích hoạt được request ra ngoài
		if (!current_user_can('upload_files')) return $content;
		set_time_limit(240);
		$preg = preg_match_all('/<img.*?src="(.*?)"/', stripslashes($content), $matches);
		if (!$preg) return $content;
		$home_host = wp_parse_url(home_url(), PHP_URL_HOST);
		foreach ($matches[1] as $image_url) {
			if (empty($image_url)) continue;
			$image_host = wp_parse_url($image_url, PHP_URL_HOST);
			if ($home_host && $image_host && strcasecmp($image_host, $home_host) === 0) continue;
			// Tải ảnh qua HTTP API của WordPress (có bảo vệ SSRF)
			$image_data = $this->horsetools_get_remote_image_data($image_url);
			if ($image_data) {
				$res = $this->horsetools_save_image_data($image_data, $post_id, $image_url);
				if ($res) {
					$url = wp_get_attachment_url($res);
					$content = str_replace($image_url, $url, $content);
				}
			}
		}
		if (isset($horsetools_options['post-up11']) && !empty($content)) {
			$content = wp_kses_post($content);
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML('<?xml encoding="UTF-8">' . $content);
			$imgs = $dom->getElementsByTagName('img');
			foreach ($imgs as $img) {
				$class = $img->getAttribute('class');
				if (strpos(strtolower($class), 'lazy') !== false) {
					$src = $img->getAttribute('data-src');
					$img->setAttribute('src', $src);
				} else {
					$src = $img->getAttribute('src');
				}
				$img->setAttribute('alt', get_the_title());
				$newImg = $dom->createElement('img');
				$newImg->setAttribute('src', $src);
				$newImg->setAttribute('alt', get_the_title());  
				$img->parentNode->replaceChild($newImg, $img);
			}
			// Serialise the body's CHILDREN, not the body element itself.
			// saveHTML($node) includes the node's own tags, so this used to
			// write "<body>…your post…</body>" into post_content on every save.
			// Browsers ignore the stray tags when rendering, which is why it
			// went unnoticed, but the stored content is corrupt: excerpts, RSS
			// and search all see them, and the block editor offers a
			// "this block contains unexpected content" recovery prompt.
			$body = $dom->getElementsByTagName('body')->item(0);
			$result = '';
			if ( $body ) {
				foreach ( $body->childNodes as $child ) {
					$result .= $dom->saveHTML( $child );
				}
			}
			libxml_clear_errors();
			if ( '' !== $result ) {
				$content = $result;
			}
		}
		remove_filter('content_save_pre', array($this, 'horsetools_post_save_images'));
		return $content;
	}
    function horsetools_save_image_data($image_data, $post_id, $original_url) {
		$post = get_post($post_id);
		if (!$post) return null;
		$url_parts = parse_url($original_url);
		$ext = pathinfo($url_parts['path'], PATHINFO_EXTENSION) ?: 'jpg';
		$upload_dir = wp_upload_dir(); 
		$filename = sanitize_title($post->post_title) . '.' . $ext; // Tên file không có phần ngẫu nhiên
		$unique_filename = wp_unique_filename($upload_dir['path'], $filename); // Kiểm tra và tạo tên file duy nhất
		$res = wp_upload_bits($unique_filename, '', $image_data);
		return $res['error'] ? null : $this->horsetools_insert_attachment($res['file'], $post_id);
	}
    function horsetools_insert_attachment($file, $id) {
        $dirs = wp_upload_dir();
        $filetype = wp_check_filetype($file);
        $attachment = array(
            'guid' => $dirs['baseurl'] . '/' . _wp_relative_upload_path($file),
            'post_mime_type' => $filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $file, $id);
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);
        return $attach_id;
    }
}
isset($horsetools_options['post-up1']) && new horsetools_save_images_hots();
# Xóa bài viết sẽ xóa luôn hình ảnh đính kèm trong post story land
if(isset($horsetools_options['post-del1'])){
function horsetools_delete_all_attached_media( $post_id ) {
		// xoa anh dai dien
		$thumbnail_id = get_post_thumbnail_id($post_id);
		if ($thumbnail_id) {
			wp_delete_attachment($thumbnail_id, true);
		}	
		// xoa anh dinh kem	
		$attachments = get_attached_media( '', $post_id );
		foreach ($attachments as $attachment) {
			wp_delete_attachment( $attachment->ID, 'true' );
		}
}
add_action( 'before_delete_post', 'horsetools_delete_all_attached_media' );
}
# anh kich thuoc goc khi them vao bai viet
//
// This was two bare update_option() calls at file scope, so they ran on every
// single request — front end included — and permanently overwrote whatever the
// site owner had chosen in Settings → Media, with no way to change it back
// while the plugin was active. Filtering the option instead leaves the stored
// value alone and stops applying the moment the toggle is turned off.
if(isset($horsetools_options['post-imgsize1'])){
	add_filter( 'pre_option_image_default_size', function () { return 'full'; } );
}
# tự động lấy ảnh đầu tiên bài viết thế vào làm ảnh đại diện
if(isset($horsetools_options['post-thum1'])){
// lay anh thu id goc tu anh thu nho
function horsetools_from_thumbnail_url($thumbnail_url) {
    global $wpdb;
    $original_id = attachment_url_to_postid($thumbnail_url);
    $filename = wp_basename($thumbnail_url);
    $thumbnail_filename = preg_replace('/-\d+x\d+(\.\w+)$/', '$1', $filename);
    $query = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s", '%' . $thumbnail_filename . '%');
    $attachment_id = $wpdb->get_var($query);
    return $attachment_id ? $attachment_id : $original_id;
}
// xu ly
function horsetools_auto_featured_image($post_id) {
	global $horsetools_options;
	$imgdua = !empty($horsetools_options['post-thum11']) ? $horsetools_options['post-thum11'] : null;
    $post = get_post($post_id);
    if ($post && !has_post_thumbnail($post->ID)) {
        $first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if (!empty($matches[1][0])) {
			$first_img = $matches[1][0];
			$default_image_id = horsetools_from_thumbnail_url($first_img);
			set_post_thumbnail($post->ID, $default_image_id);
		}
		else {
            $default_image_url = $imgdua;
            $default_image_id = attachment_url_to_postid($default_image_url);
            if ($default_image_id) {
                set_post_thumbnail($post->ID, $default_image_id);
            }
        }
    }
}
add_action('publish_post', 'horsetools_auto_featured_image');
}
# chuc nang nhan ban bai viet va trang
if(isset($horsetools_options['post-dup1'])){
// Tạo liên kết nhân bản
function horsetools_quick_duplicate_post_button($actions, $post) {
	global $horsetools_options;
    if (isset($horsetools_options['post-dup-posttype']) && in_array($post->post_type, $horsetools_options['post-dup-posttype'])) {
        $actions['duplicate'] = sprintf(
            '<a href="%s" title="%s" rel="permalink">%s</a>',
            esc_url(wp_nonce_url(admin_url('admin.php?action=duplicate_post&post=' . $post->ID), 'duplicate-post_' . $post->ID)),
            esc_attr__('Duplicate content', 'horse-tools'),
            __('Duplicate', 'horse-tools')
        );
    }
    return $actions;
}
add_filter('post_row_actions', 'horsetools_quick_duplicate_post_button', 10, 2);
add_filter('page_row_actions', 'horsetools_quick_duplicate_post_button', 10, 2);
// Tạo chức năng nhân bản nhanh
function horsetools_quick_duplicate_post_action() {
    if (isset($_GET['action']) && $_GET['action'] == 'duplicate_post' && isset($_GET['post'])) {
        $post_id = absint($_GET['post']);
        check_admin_referer('duplicate-post_' . $post_id);
        $post = get_post($post_id);
        if ( ! $post || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_die( esc_html__( 'You are not allowed to duplicate this item.', 'horse-tools' ) );
        }
        $post_type_object = get_post_type_object( $post->post_type );
        if ( ! $post_type_object || ! current_user_can( $post_type_object->cap->create_posts ) ) {
            wp_die( esc_html__( 'You are not allowed to create this post type.', 'horse-tools' ) );
        }
        if ($post) {
            $new_post = array(
                'post_title'     => $post->post_title . ' ' . __('(duplicate)', 'horse-tools'),
                'post_status'    => 'draft',
                'post_type'      => $post->post_type,
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_content'   => wp_slash($post->post_content),
                'post_excerpt'   => $post->post_excerpt,
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order,
            );
            $new_post_id = wp_insert_post($new_post);
            if (is_wp_error($new_post_id)) {
                wp_die(__($new_post_id->get_error_message()));
            }
            $taxonomies = array_map('sanitize_text_field', get_object_taxonomies($post->post_type));
            if (!empty($taxonomies) && is_array($taxonomies)) {
                foreach ($taxonomies as $taxonomy) {
                    $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                    wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                }
            }
            $post_meta_keys = get_post_custom_keys($post_id);
            if (!empty($post_meta_keys)) {
                foreach ($post_meta_keys as $meta_key) {
                    $meta_values = get_post_custom_values($meta_key, $post_id);

                    foreach ($meta_values as $meta_value) {
                        $meta_value = maybe_unserialize($meta_value);
                        update_post_meta($new_post_id, $meta_key, wp_slash($meta_value));
                    }
                }
            }
            $redirect_url = wp_get_referer(); // Lấy URL referer
			if ($redirect_url) {
				wp_redirect(esc_url_raw($redirect_url)); // Quay lại trang referer
			} else {
				// Nếu không tìm thấy referer, quay lại trang quản lý bài viết
				wp_redirect(esc_url_raw(admin_url('edit.php?post_type=' . $post->post_type)));
			}
			exit;
        }
    }
}
add_action('admin_action_duplicate_post', 'horsetools_quick_duplicate_post_action');
}
# Xóa slug category cha khỏi đường dẫn
if(isset($horsetools_options['post-link1'])){
function horsetools_no_category_parents($url, $term, $taxonomy) {
    if ($taxonomy == 'category') {
        $term_nicename = $term->slug;
        $url = trailingslashit(get_option('home')) . user_trailingslashit($term_nicename, 'category');
        $url = str_replace('/category/', '/', $url); // Loại bỏ "/category" khỏi URL
    }
    return $url;
}
add_filter('term_link', 'horsetools_no_category_parents', 1000, 3);

function horsetools_no_category_parents_rewrite_rules($flash = false) {
    $terms = get_terms(array(
        'taxonomy' => 'category',
        'post_type' => 'post',
        'hide_empty' => false,
    ));
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $term_slug = $term->slug;
            $new_term_slug = str_replace('category/', '', $term_slug); // Loại bỏ "category/" khỏi slug
            add_rewrite_rule($new_term_slug . '/?$', 'index.php?category_name=' . $term_slug, 'top');
            add_rewrite_rule($new_term_slug . '/page/([0-9]{1,})/?$', 'index.php?category_name=' . $term_slug . '&paged=$matches[1]', 'top');
            add_rewrite_rule($new_term_slug . '/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', 'index.php?category_name=' . $term_slug . '&feed=$matches[1]', 'top');
        }
    }
    if ($flash == true)
        flush_rewrite_rules(false);
}
add_action('init', 'horsetools_no_category_parents_rewrite_rules');
// chuyen huong có category sang không có
function horsetools_redirect_old_category_urls() {
    if (is_category() && strpos($_SERVER['REQUEST_URI'], '/category/') !== false) {
        $new_url = preg_replace('/\/category\//', '/', $_SERVER['REQUEST_URI'], 1);
        wp_redirect(home_url($new_url), 301); // 301 redirect for permanent move
        exit();
    }
}
add_action('template_redirect', 'horsetools_redirect_old_category_urls');
function horsetools_new_category_edit_success() {
    horsetools_no_category_parents_rewrite_rules(true);
}
add_action('created_category', 'horsetools_new_category_edit_success');
add_action('edited_category', 'horsetools_new_category_edit_success');
add_action('delete_category', 'horsetools_new_category_edit_success');
}
# Xóa slug tag khỏi đường dẫn
if(isset($horsetools_options['post-link2'])){
function horsetools_post_tag_permalink( $url, $term, $taxonomy ){
    switch ($taxonomy):
        case 'post_tag':
            $taxonomy_slug = 'tag';
            if(strpos($url, $taxonomy_slug) === FALSE) break;
            $url = str_replace('/' . $taxonomy_slug, '', $url);
            break;
    endswitch;
    return $url;
}
add_filter( 'term_link', 'horsetools_post_tag_permalink', 10, 3 );
// rewrite rules
function horsetools_post_tag_rewrite_rules($flash = false) {
    $terms = get_terms( array(
        'taxonomy' => 'post_tag',
        'post_type' => 'post',
        'hide_empty' => false,
    ));
    if($terms && !is_wp_error($terms)){
        $siteurl = esc_url(home_url('/'));
        foreach ($terms as $term){
            $term_slug = $term->slug;
            $baseterm = str_replace($siteurl,'',get_term_link($term->term_id,'post_tag'));
            add_rewrite_rule($baseterm.'?$','index.php?tag='.$term_slug,'top');
            add_rewrite_rule($baseterm.'page/([0-9]{1,})/?$', 'index.php?tag='.$term_slug.'&paged=$matches[1]','top');
            add_rewrite_rule($baseterm.'(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', 'index.php?tag='.$term_slug.'&feed=$matches[1]','top');
        }
    }
    if ($flash == true)
        flush_rewrite_rules(false);
}
add_action('init', 'horsetools_post_tag_rewrite_rules');
// chuyen huong tag sang khong tag
function horsetools_redirect_old_post_tag_urls() {
    if (is_tag() && strpos($_SERVER['REQUEST_URI'], '/tag/') !== false) {
        $new_url = preg_replace('/\/tag\//', '/', $_SERVER['REQUEST_URI'], 1);
        wp_redirect(home_url($new_url), 301); // 301 redirect for permanent move
        exit();
    }
}
add_action('template_redirect', 'horsetools_redirect_old_post_tag_urls');
// sửa lỗi khi tạo mới tag bị 404
function horsetools_new_post_tag_edit_success( $term_id, $taxonomy ) {
    horsetools_post_tag_rewrite_rules(true);
}
add_action( 'created_post_tag', 'horsetools_new_post_tag_edit_success', 10, 2 );
}
# thêm .html cho page
if(isset($horsetools_options['post-html1'])){
function horsetools_change_page_permalink() {
    global $wp_rewrite;
    if ( strstr($wp_rewrite->get_page_permastruct(), '.html') != '.html' )
    $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
}
add_action('init', 'horsetools_change_page_permalink', -1);
}


# Thêm mô tả cho hình ảnh khi tải lên
if(isset($horsetools_options['post-alt1'])){
function horsetools_add_description_to_media($attachment_ID) {
    $post = get_post($attachment_ID);
    if ($post->post_type === 'attachment' && empty(get_post_meta($attachment_ID, '_wp_attachment_image_alt', true))) {
        $post_title = get_the_title($post->post_parent);
        update_post_meta($attachment_ID, '_wp_attachment_image_alt', $post_title);
    }
}
add_action('add_attachment', 'horsetools_add_description_to_media');
}
# thêm nofollow và _blank cho đường dẫn bên ngoài ở bài viết
if(isset($horsetools_options['post-out1'])){
function horsetools_target_blank_to_nofollow_and_external() {
    echo '<span id="horseglobal"></span>';
}
add_action('wp_footer', 'horsetools_target_blank_to_nofollow_and_external');
}
# su dung shortcode title
if(isset($horsetools_options['post-other1'])){
	add_filter( 'the_title', 'do_shortcode' );
	add_filter( 'single_post_title', 'do_shortcode' );
	add_filter( 'wpseo_title', 'do_shortcode' );
	add_filter( 'wpseo_metadesc', 'do_shortcode' );
	add_filter( 'wpseo_opengraph_title', 'do_shortcode' );
	add_filter( 'wpseo_opengraph_desc', 'do_shortcode' );
	add_filter( 'wpseo_opengraph_site_name', 'do_shortcode' );
	add_filter( 'wpseo_twitter_title', 'do_shortcode' );
	add_filter( 'wpseo_twitter_description', 'do_shortcode' );
	add_filter( 'the_excerpt', 'do_shortcode' );
}
# hien thị bai viet vua chinh sua dau tien
if(isset($horsetools_options['post-other2'])){
function horsetools_orderby_modified_posts( $query ) {
    if( $query->is_main_query() && !is_admin() ) {
	if ( $query->is_home() || $query->is_category() || $query->is_tag() ) {
            $query->set( 'orderby', 'modified' );
            $query->set( 'order', 'desc' );
	}
    }
}
add_action( 'pre_get_posts', 'horsetools_orderby_modified_posts' );
}
# ẩn các bài viết có id chuyen muc khỏi trang chu
if(isset($horsetools_options['post-hiden1'])){
function horsetools_categories_hiden_home($query){
	global $horsetools_options;
    if ($query->is_home() && $query->is_main_query() && !empty($horsetools_options['post-hiden11'])) {
        $id_cate = $horsetools_options['post-hiden11'];
        $id_cate_hiden = explode(',', $id_cate);
        $query->set('category__not_in', $id_cate_hiden);
    }
}
add_action('pre_get_posts', 'horsetools_categories_hiden_home');
}
# Image lightbox — GLightbox or PhotoSwipe (both MIT-licensed, free), chosen per site.
if (isset($horsetools_options['post-fancy1'])){
// Where the lightbox runs, from the scope option (posts / posts+pages / all singular).
function horsetools_lb_on(){
	global $horsetools_options;
	$scope = ! empty( $horsetools_options['post-fancy-scope'] ) ? $horsetools_options['post-fancy-scope'] : 'post';
	if ( 'all' === $scope )  { return is_singular(); }
	if ( 'page' === $scope ) { return is_singular( array( 'post', 'page' ) ); }
	return is_singular( 'post' );
}
// Which engine is selected.
function horsetools_lb_engine(){
	global $horsetools_options;
	return ( ! empty( $horsetools_options['post-fancy-engine'] ) && 'photoswipe' === $horsetools_options['post-fancy-engine'] ) ? 'photoswipe' : 'glightbox';
}
// Enqueue the chosen library. GLightbox is a normal (UMD) script; PhotoSwipe is
// an ES module, so only its CSS is enqueued here and the JS is an inline module.
function horsetools_lb_enqueue(){
	if ( ! horsetools_lb_on() ) { return; }
	if ( 'photoswipe' === horsetools_lb_engine() ) {
		wp_enqueue_style( 'ht-photoswipe', HORSETOOLS_URL . 'link/photoswipe/photoswipe.css', array(), HORSETOOLS_VERSION );
	} else {
		wp_enqueue_script( 'ht-glightbox', HORSETOOLS_URL . 'link/glightbox/glightbox.min.js', array(), HORSETOOLS_VERSION, true );
		wp_enqueue_style( 'ht-glightbox', HORSETOOLS_URL . 'link/glightbox/glightbox.min.css', array(), HORSETOOLS_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'horsetools_lb_enqueue' );
// Backdrop theme + accent colour + a zoom-in cursor, targeting the active engine.
function horsetools_lb_theme_css(){
	if ( ! horsetools_lb_on() ) { return; }
	global $horsetools_options;
	$theme  = ! empty( $horsetools_options['post-fancy-theme'] ) ? $horsetools_options['post-fancy-theme'] : 'dark';
	$accent = ! empty( $horsetools_options['post-fancy-accent'] ) ? trim( $horsetools_options['post-fancy-accent'] ) : '';
	$themes = array(
		'dark'   => array( 'bg' => 'rgba(20,20,22,.97)',    'blur' => false ),
		'cinema' => array( 'bg' => '#000000',               'blur' => false ),
		'light'  => array( 'bg' => 'rgba(248,248,250,.97)', 'blur' => false ),
		'blur'   => array( 'bg' => 'rgba(20,20,22,.55)',    'blur' => true ),
	);
	$t = isset( $themes[ $theme ] ) ? $themes[ $theme ] : $themes['dark'];
	// Only trust the accent if it is a plain colour literal (belt-and-suspenders
	// on top of the colour sanitiser) so it can never break out of the CSS.
	$ok_accent = ( '' !== $accent && preg_match( '/^(#[0-9a-fA-F]{3,8}|rgba?\([0-9,.\s]+\))$/', $accent ) );

	// !important is required: our <style> prints before the engine's own bundled
	// CSS, and both target the same single class (.goverlay / .pswp) at equal
	// specificity — so without it the library's default backdrop wins the cascade
	// and every theme looks identical. The engines don't use !important here, so
	// this reliably wins regardless of stylesheet order.
	if ( 'photoswipe' === horsetools_lb_engine() ) {
		$css = '.pswp{--pswp-bg:' . $t['bg'] . ' !important;}';
		if ( $t['blur'] )   { $css .= '.pswp__bg{backdrop-filter:blur(14px) !important;-webkit-backdrop-filter:blur(14px) !important;}'; }
		if ( $ok_accent )   { $css .= '.pswp__button--arrow .pswp__icn,.pswp__button{color:' . $accent . ' !important;}'; }
	} else {
		$css = '.goverlay{background:' . $t['bg'] . ' !important;}';
		if ( $t['blur'] )   { $css .= '.goverlay{backdrop-filter:blur(14px) !important;-webkit-backdrop-filter:blur(14px) !important;}'; }
		if ( $ok_accent )   { $css .= '.glightbox-container .gnext svg,.glightbox-container .gprev svg,.glightbox-container .gclose svg{color:' . $accent . ' !important;}'; }
	}
	$css .= '.ht-lightbox img{cursor:zoom-in;}';
	echo '<style id="ht-lb-theme">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- preset values + colour-validated accent
}
add_action( 'wp_head', 'horsetools_lb_theme_css', 6 );
// Footer initialiser for the chosen engine.
function horsetools_lb_script(){
	global $horsetools_options;
	if ( ! horsetools_lb_on() ) { return; }
	$engine  = horsetools_lb_engine();
	$gallery = isset( $horsetools_options['post-fancy11'] ) ? 'true' : 'false';
	$cap     = ! empty( $horsetools_options['post-fancy-caption'] ) ? $horsetools_options['post-fancy-caption'] : 'alt';
	$anim    = ! empty( $horsetools_options['post-fancy-anim'] ) ? $horsetools_options['post-fancy-anim'] : 'zoom';
	$anim    = in_array( $anim, array( 'zoom', 'fade', 'none' ), true ) ? $anim : 'zoom';
	$slide   = ! empty( $horsetools_options['post-fancy-slide'] ) ? $horsetools_options['post-fancy-slide'] : 'slide';
	$slide   = in_array( $slide, array( 'slide', 'fade', 'zoom', 'none' ), true ) ? $slide : 'slide';
	$loop    = isset( $horsetools_options['post-fancy-loop'] ) ? 'true' : 'false';
	// Caption expression, evaluated against a DOM element named `img`.
	switch ( $cap ) {
		case 'none':       $cap_js = "''"; break;
		case 'title':      $cap_js = "(img.getAttribute('title')||'')"; break;
		case 'figcaption': $cap_js = "((img.closest('figure')&&img.closest('figure').querySelector('figcaption')?img.closest('figure').querySelector('figcaption').textContent.trim():'')||img.getAttribute('alt')||'')"; break;
		default:           $cap_js = "(img.getAttribute('alt')||'')";
	}

	if ( 'photoswipe' === $engine ) {
		$psl = HORSETOOLS_URL . 'link/photoswipe/photoswipe-lightbox.esm.min.js';
		$ps  = HORSETOOLS_URL . 'link/photoswipe/photoswipe.esm.min.js';
		$pc  = HORSETOOLS_URL . 'link/photoswipe/photoswipe-dynamic-caption.esm.min.js';
		?>
		<script type="module">
		import PhotoSwipeLightbox from <?php echo wp_json_encode( $psl ); ?>;
		<?php if ( 'none' !== $cap ) : ?>
		import PhotoSwipeDynamicCaption from <?php echo wp_json_encode( $pc ); ?>;
		<?php endif; ?>
		(function(){
			var isImg = function(u){ return /\.(jpe?g|png|gif|webp|avif|bmp|svg)(\?|#|$)/i.test(u||''); };
			document.querySelectorAll('.ht-lightbox img').forEach(function(img){
				var cap = <?php echo $cap_js; // phpcs:ignore ?>;
				var a = img.closest('a'), href, target;
				if (a){ href = a.getAttribute('href')||''; if(!isImg(href)) return; target = a; }
				else { href = img.getAttribute('data-src')||img.getAttribute('src'); target = document.createElement('a'); target.setAttribute('href', href); img.parentNode.insertBefore(target, img); target.appendChild(img); }
				target.classList.add('ht-pswp');
				// Prefer the intrinsic size; fall back to the rendered box (keeps the
				// aspect ratio right even before the image has finished loading).
				var w = img.naturalWidth || img.offsetWidth || parseInt(img.getAttribute('width'),10) || 1600;
				var h = img.naturalHeight || img.offsetHeight || parseInt(img.getAttribute('height'),10) || Math.round(w*0.66);
				target.setAttribute('data-pswp-width', w);
				target.setAttribute('data-pswp-height', h);
				if (cap) target.setAttribute('data-pswp-caption', cap);
			});
			var lb = new PhotoSwipeLightbox({
				gallery: '.ht-lightbox',
				children: 'a.ht-pswp',
				showHideAnimationType: <?php echo wp_json_encode( $anim ); ?>,
				pswpModule: function(){ return import(<?php echo wp_json_encode( $ps ); ?>); }
			});
			<?php if ( 'none' !== $cap ) : ?>
			new PhotoSwipeDynamicCaption(lb, { type: 'auto', captionContent: function(slide){ var e = slide.data.element; return e ? (e.getAttribute('data-pswp-caption')||'') : ''; } });
			<?php endif; ?>
			lb.init();
		})();
		</script>
		<?php
	} else {
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function(){
			var grouped = <?php echo $gallery; ?>, i = 0;
			var isImg = function(u){ return /\.(jpe?g|png|gif|webp|avif|bmp|svg)(\?|#|$)/i.test(u||''); };
			document.querySelectorAll('.ht-lightbox img').forEach(function(img){
				var cap = <?php echo $cap_js; // phpcs:ignore ?>;
				var a = img.closest('a'), href, target;
				if (a){ href = a.getAttribute('href')||''; if(!isImg(href)) return; target = a; }
				else { href = img.getAttribute('data-src')||img.getAttribute('src'); target = document.createElement('a'); target.setAttribute('href', href); img.parentNode.insertBefore(target, img); target.appendChild(img); }
				target.classList.add('ht-glb');
				// One shared gallery for prev/next, or a unique one per image so each opens alone.
				target.setAttribute('data-gallery', grouped ? 'ht' : ('ht' + (i++)));
				if (cap) target.setAttribute('data-title', cap);
			});
			if (window.GLightbox){
				GLightbox({ selector: '.ht-glb', openEffect: <?php echo wp_json_encode( $anim ); ?>, closeEffect: <?php echo wp_json_encode( $anim ); ?>, slideEffect: <?php echo wp_json_encode( $slide ); ?>, loop: <?php echo $loop; ?>, zoomable: true, touchNavigation: true });
			}
		});
		</script>
		<?php
	}
}
add_action( 'wp_footer', 'horsetools_lb_script' );
// Wrap the main content so only its images are targeted (not archives/feeds).
function horsetools_lb_addiv( $content ) {
    if ( ! horsetools_lb_on() || ! in_the_loop() || ! is_main_query() || is_feed() ) {
        return $content;
    }
    return '<div class="ht-lightbox">'. $content .'</div>';
}
add_filter( 'the_content', 'horsetools_lb_addiv' );
}


















/* FAQ schema: reads the FAQ section a post already has and publishes the
   matching JSON-LD. Part of the CONTENT module. */
require_once HORSETOOLS_DIR . 'inc/faq-schema.php';

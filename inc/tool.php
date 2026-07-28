<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# bật editor classic
if (isset($horsetools_options['tool-edit1'])){
add_filter('use_block_editor_for_post', '__return_false');
}
# them chuc nang cho classic
if (isset($horsetools_options['tool-edit11'])){
// them menu hang 1
function horsetools_mce_editor_buttons_mot( $buttons ) {
	if (is_admin()) {
		$alignright_index = array_search('alignright', $buttons);
		if ($alignright_index !== false) {
			array_splice($buttons, $alignright_index + 1, 0, 'unlink');
			array_splice($buttons, $alignright_index + 1, 0, 'alignjustify');
		} else {
			$buttons[] = 'alignjustify';
			$buttons[] = 'unlink';
		}
	}
    return $buttons;
}
add_filter( 'mce_buttons', 'horsetools_mce_editor_buttons_mot' );
// them menu hang 2
function horsetools_tinymce_plugin( $plugin_array ) {
	if (is_admin()) {
		$table_plugin_url = HORSETOOLS_URL . 'link/tinyMCE/table/plugin.min.js';
		$plugin_array['table'] = $table_plugin_url;
		$shortcode_plugin_url = HORSETOOLS_URL . 'link/tinyMCE/shortcode/plugin.min.js';
		$plugin_array['horsetools_dropbutton'] = $shortcode_plugin_url;
		$search_plugin_url = HORSETOOLS_URL . 'link/tinyMCE/searchreplace/plugin.min.js';
		$plugin_array['searchreplace'] = $search_plugin_url;
		$print_plugin_url = HORSETOOLS_URL . 'link/tinyMCE/print/plugin.min.js';
		$plugin_array['print'] = $print_plugin_url;
	}
    return $plugin_array;
}
add_filter( 'mce_external_plugins', 'horsetools_tinymce_plugin' );
// add
function horsetools_mce_editor_buttons_hai( $buttons ) {
	if (is_admin()) {
		// get_current_screen() returns null in AJAX contexts where is_admin()
		// is still true — e.g. a plugin rendering wp_editor() over AJAX — and
		// reading ->post_type off null is a PHP 8 error.
		$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		array_unshift( $buttons, 'fontselect' );
		array_unshift( $buttons, 'fontsizeselect' );
		if ( $current_screen && ( $current_screen->post_type == 'post' || $current_screen->post_type == 'page' ) ) {
		array_push( $buttons, 'horsetools_dropbutton' );
		}
		array_push( $buttons, 'separator', 'table' );
	}
    return $buttons;
}
add_filter( 'mce_buttons_2', 'horsetools_mce_editor_buttons_hai' );
// them menu hang 3
function horsetools_mce_editor_buttons_ba( $buttons ) {
	if (is_admin()) {
		$buttons[] = 'superscript';
		$buttons[] = 'subscript';
		$buttons[] = 'cut';
		$buttons[] = 'copy';
		$buttons[] = 'paste';
		$buttons[] = 'newdocument';
		$buttons[] = 'searchreplace';
		$buttons[] = 'print';
	}
	return $buttons;
}
add_filter( 'mce_buttons_3', 'horsetools_mce_editor_buttons_ba' );
// chuyen pt sang size
if ( ! function_exists( 'horsetools_mce_text_sizes' ) ) {
    function horsetools_mce_text_sizes( $initArray ){
        $initArray['fontsize_formats'] = "8px 10px 12px 14px 16px 20px 24px 28px 32px 36px 48px 60px 72px 96px";
        return $initArray;
    }
}
add_filter( 'tiny_mce_before_init', 'horsetools_mce_text_sizes', 99);
}
# them nut add classic vao phan quan lý bài viết và trang
if (isset($horsetools_options['tool-edit12']) && !isset($horsetools_options['tool-edit1'])){
function horsetools_add_classic_editor( $actions, $post){
	if ( 'trash' === $post->post_status || ! post_type_supports( $post->post_type, 'editor' ) ) {
		return $actions;
	}
	$edit_url = get_edit_post_link( $post->ID, 'raw' );
	if ( ! $edit_url ) {
		return $actions;
	}
	if ($post->post_type == 'page' || $post->post_type == 'post') {
		$edit_url = add_query_arg( 'open-classic', '', $edit_url );
		$title       = _draft_or_post_title( $post->ID );
		$edit_action = array(
			'classic' => sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( $edit_url ),
				esc_attr( sprintf(__('Classic editing', 'horse-tools'), $title) ),
				sprintf(__('Editor classic', 'horse-tools')),
			),
		);
		$edit_offset = array_search( 'edit', array_keys( $actions ), true );
		array_splice( $actions, $edit_offset + 1, 0, $edit_action );
	}
	return $actions;
}
add_filter( 'page_row_actions', 'horsetools_add_classic_editor', 15, 2 );
add_filter( 'post_row_actions', 'horsetools_add_classic_editor', 15, 2 );
// nut classic o quan ly bai viet trang
function horsetools_addbutton_classic() {
    global $pagenow;
    if (($pagenow === 'edit.php' && !isset($_GET['post_type'])) || ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') || ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'post')){
		if($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page'){
			$new_post_url = admin_url('post-new.php?post_type=page&open-classic');
		} else {
		$new_post_url = admin_url('post-new.php?open-classic');
		}
        echo '<script>
            jQuery(document).ready(function($) {
                var newButton = \'<a href="'. $new_post_url .'" class="page-title-action" style="margin-left:10px;">'. __('Classic Editor', 'horse-tools') .'</a>\';
                $(".wrap h1").append(newButton);
            });
        </script>';
    }
}
add_action('admin_footer', 'horsetools_addbutton_classic');
// chuyen qua classic
if ( isset( $_GET['open-classic'] )) {
	add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
}
// thêm open-class sau khi luu
/**
 * Keep the ?open-classic flag across a save.
 *
 * This used to hook save_post, call wp_redirect() and exit() — from inside
 * wp_insert_post(). That aborted the rest of the save: every save_post callback
 * registered after this one never ran (so third-party meta silently vanished),
 * wp_after_insert_post() never fired, sticky-post handling and post-lock
 * release were skipped, and redirect_post() never ran so the "Post updated"
 * notice disappeared. On a block-editor save it answered a REST request with a
 * 302; on cron it killed scheduled publishing; on an import it aborted the run.
 *
 * redirect_post_location is the hook that exists for this. It runs at the right
 * moment, changes only the destination, and cannot break anything else.
 */
function horsetools_keep_open_classic_arg( $location ) {
    if ( isset( $_POST['open-classic'] ) || isset( $_GET['open-classic'] ) ) {
        $location = add_query_arg( 'open-classic', '1', $location );
    }
    return $location;
}
add_filter( 'redirect_post_location', 'horsetools_keep_open_classic_arg' );
}
# bật widget classic
if (isset($horsetools_options['tool-widget1'])){
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
}
# chăn copy nội dung khoa tat ca cac phim
function horsetools_lockcop_scripts() {
  global $horsetools_options;
  if (isset($horsetools_options['tool-mana2']) && !current_user_can('manage_options')){
  wp_enqueue_script( 'lockcop', HORSETOOLS_URL . 'link/lockcop.js', array(), HORSETOOLS_VERSION);
  wp_enqueue_style( 'lockcop', HORSETOOLS_URL . 'link/lockcop.css', array(), HORSETOOLS_VERSION);
  }
}
add_action( 'wp_enqueue_scripts', 'horsetools_lockcop_scripts' );
# copy noi dung da dat truoc
function horsetools_copyset_scripts() { 
	global $horsetools_options; 
	$text = !empty($horsetools_options['tool-mana22']) ? $horsetools_options['tool-mana22'] : __('You have successfully copied', 'horse-tools');
	if (isset($horsetools_options['tool-mana21']) && !current_user_can('manage_options')){
	?>
	<script>
	jQuery(document).ready(function($) {
	<?php if (isset($horsetools_options['tool-mana23'])){ ?>
		$(document).on('copy', function(e){
            var newText = "\n<?php echo esc_js( $text ); ?>";
            var clipboardData = e.originalEvent.clipboardData || window.clipboardData || e.clipboardData;
            var originalText = window.getSelection().toString(); 
            clipboardData.setData('text', originalText + newText);
            e.preventDefault(); 
        });
	<?php } else { ?>
		$(document).on('copy', function(e){
			e.preventDefault();
			var newText = "<?php echo esc_js( $text ); ?>";
			var clipboardData = e.originalEvent.clipboardData || window.clipboardData || e.clipboardData;
			clipboardData.setData('text', newText);
		});
	<?php } ?>
	});
	</script>
	<?php
	}
}
add_action( 'wp_footer', 'horsetools_copyset_scripts' );
# tắt những công cụ không cần thiết
function horsetools_remove_appwp_admin(){
	global $horsetools_options, $menu;
	if (is_array($menu)) {
		echo '<style>';
		foreach ($menu as $index => $item) {
			if (isset($horsetools_options['tool-hiden'. $index])){
				echo '#'. $item[5] .'{display:none;}';
			}
		}
		echo '</style>';
	}
}
add_action( 'admin_head', 'horsetools_remove_appwp_admin');
# tắt tự động cập nhật
function horsetools_dis_update_full() {
	global $horsetools_options;
	
	// Chặn cập nhật core WP
	if (isset($horsetools_options['tool-upload1'])){
		add_filter('auto_update_core', '__return_false');
		add_filter( 'pre_option_update_core', '__return_null' );
		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', '_maybe_update_core' );
		wp_clear_scheduled_hook( 'wp_version_check' );
	}

	// Chặn cập nhật gói ngôn ngữ
	if (isset($horsetools_options['tool-upload2'])){
		add_filter('auto_update_translation', '__return_false');
		add_filter( 'pre_site_transient_update_languages', '__return_null' );
		add_filter( 'site_transient_update_languages', '__return_null' );
		remove_action('load-update-core.php', 'wp_update_languages');
		remove_action('admin_init', '_maybe_update_languages');
		remove_action('wp_update_languages', 'wp_update_languages');
		wp_clear_scheduled_hook('wp_update_languages');
	}

    // Chặn cập nhật theme
	if (isset($horsetools_options['tool-upload3'])){
		add_filter('auto_update_theme', '__return_false');
		add_filter( 'pre_site_transient_update_themes', '__return_null' );
		remove_action('load-themes.php', 'wp_update_themes');
		remove_action('load-update.php', 'wp_update_themes');
		remove_action('admin_init', '_maybe_update_themes');
		remove_action('wp_update_themes', 'wp_update_themes');
		remove_action('load-update-core.php', 'wp_update_themes');
		wp_clear_scheduled_hook('wp_update_themes');
	}

    // Chặn cập nhật plugin
	if (isset($horsetools_options['tool-upload4'])){
		add_filter('auto_update_plugin', '__return_false');
		add_filter( 'pre_site_transient_update_plugins', '__return_null' );
		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );
	}
	
	// Loại bỏ thông báo cập nhật & bảo trì
	if (isset($horsetools_options['tool-upload5'])){
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'network_admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag' );
		remove_action( 'network_admin_notices', 'maintenance_nag' );
	}

    // Chặn cập nhật qua API
	if (isset($horsetools_options['tool-upload6'])){
		remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_auto_update_core' );
		wp_clear_scheduled_hook( 'wp_maybe_auto_update' );
		remove_all_filters( 'plugins_api' );
	}
}
add_action('init', 'horsetools_dis_update_full');

# thêm tiny editor vao description
if ( isset($horsetools_options['tool-mana3'])){
function horsetools_tiny_description($tag){
    ?>
    <table class="form-table">
        <tr class="form-field">
            <th scope="row" valign="top"><label for="description"></label></th>
            <td>
                <?php
                    $settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '10', 'textarea_name' => 'description' );
                    wp_editor(html_entity_decode($tag->description), 'horsetools_tiny_description', $settings);
                ?>
                <br />
                <span class="description"></span>
            </td>
        </tr>
    </table>
    <?php
}
add_filter('category_edit_form_fields', 'horsetools_tiny_description');
add_filter('product_cat_edit_form_fields', 'horsetools_tiny_description');
// xoa mac dinh
function horsetools_remove_default_category_description(){
    global $current_screen;
    if ($current_screen->taxonomy == 'category' || $current_screen->taxonomy == 'product_cat') {
    echo '<style>textarea#description{display:none}</style>';
    }
}
add_action('admin_head', 'horsetools_remove_default_category_description');
// Mo rong danh sach the HTML cho phep khi luu mo ta term.
// Truoc day cho nay go han wp_filter_kses/wp_kses_data ra khoi pre_term_description
// va term_description, tat KSES tren toan site cho moi taxonomy term description.
// Dieu do cho phep mot Editor luu <script> vao mo ta chuyen muc va script se chay
// trong phien cua Administrator (stored XSS -> leo thang dac quyen).
// Thay vao do chi noi rong tag cho phep trong ngu canh 'pre_term_description',
// KSES van hoat dong.
function horsetools_term_description_allowed_html($tags, $context) {
    if ('pre_term_description' !== $context) {
        return $tags;
    }
    return wp_kses_allowed_html('post');
}
add_filter('wp_kses_allowed_html', 'horsetools_term_description_allowed_html', 10, 2);
}

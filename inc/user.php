<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# loc bai viet và hình ảnh của user
if (isset($horsetools_options['user-post1'])){
function horsetools_posts_useronly( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
        if ( !current_user_can('manage_options') ) {
            global $current_user;
            $wp_query->set( 'author', $current_user->ID );
        }
    }
}
add_filter('parse_query', 'horsetools_posts_useronly' );
// hinh anh media chi admin moi thay het
function horsetools_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can('manage_options') ) {
        $query['author'] = $user_id;
    }
    return $query;
}
add_filter( 'ajax_query_attachments_args', 'horsetools_user_attachments' );
}

# chi admin mới vào được trang quản trị
if (isset($horsetools_options['user-wp1'])){
function horsetools_restrict_admin_access() {
    if (is_user_logged_in()) {
        if (is_admin() && !defined('DOING_AJAX') && !current_user_can('manage_options')) {
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('admin_init', 'horsetools_restrict_admin_access');
}
# tuy chon tắt thanh bar
if(isset($horsetools_options['user-bar1'])){
function horsetools_disable_admin_bar() {
    global $horsetools_options;
    if (isset($horsetools_options['user-bar11'])) {
        if ($horsetools_options['user-bar11'] == 'All') {
            show_admin_bar(false);
        } elseif ($horsetools_options['user-bar11'] == 'User' && !current_user_can('manage_options')) {
            show_admin_bar(false);
        }
    }
}
add_action('after_setup_theme', 'horsetools_disable_admin_bar');
}
# chức năng upload avtatar
if (isset($horsetools_options['user-upav1'])){
function horsetools_avatar_scripts(){
    wp_enqueue_media();
	wp_enqueue_script('horsetools-avatar', HORSETOOLS_URL . 'link/upload-avatar.js', array(), HORSETOOLS_VERSION);
}
add_action('admin_enqueue_scripts', 'horsetools_avatar_scripts');
function horsetools_profile_fields( $user ) {
    // Always an attachment ID. It is echoed into the admin user-edit screen,
    // where an administrator views it — so a raw string here was a path from
    // the lowest role straight to script running in an admin's session.
    $horsetools_pic = ($user !== 'add-new-user') ? absint( get_user_meta($user->ID, 'horsetoolspic', true) ) : 0;
    $image_src = '';
    if ( $horsetools_pic ) {
        $image = wp_get_attachment_image_src($horsetools_pic, 'medium');
        $image_src = $image ? $image[0] : '';
    }
    ?>
    <div class="thongtin-av">
        <div style="margin-top:20px;">
            <img id="horsetools-img" src="<?php echo esc_url( $image_src ); ?>" style="<?php echo empty($horsetools_pic) ? 'display:none;' : ''; ?>" />
        </div>
        <div class="ht-avgrid">
            <input type="button" data-id="horsetools_image_id" data-src="horsetools-img" class="button horsetools-image" name="horsetools_image" id="horsetools-image" value="<?php _e('Upload avatars', 'horse-tools') ?>" />
            <input style="margin-left:10px;" type="button" id="reset-hinh-anh" class="button" value="<?php _e('Delete', 'horse-tools'); ?>" />
            <input type="hidden" class="button" name="horsetools_image_id" id="horsetools_image_id" value="<?php echo esc_attr( $horsetools_pic ); ?>" />
        </div>
    </div>
	<style>
	#horsetools-img{
		margin-bottom:10px;
		width:100px;
		height:100px;
		object-fit: cover;
		object-position: 50% 50%;
	}
	.ht-avgrid{display:flex}
	</style>
    <?php
}
add_action('show_user_profile', 'horsetools_profile_fields');
add_action('edit_user_profile', 'horsetools_profile_fields');
add_action('user_new_form', 'horsetools_profile_fields');
function horsetools_profile_update($user_id){
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return;
    }
    // Store an attachment ID and nothing else. This value is later rendered
    // into the admin user-edit screen and into avatar markup on the front end,
    // so anything free-form here is stored XSS that a Subscriber can plant and
    // an Administrator triggers just by opening the user's profile.
    $horsetools_pic = isset($_POST['horsetools_image_id'])
        ? absint( wp_unslash( $_POST['horsetools_image_id'] ) )
        : 0;
    if ( $horsetools_pic && 'attachment' !== get_post_type( $horsetools_pic ) ) {
        $horsetools_pic = 0;
    }
    update_user_meta($user_id, 'horsetoolspic', $horsetools_pic);
}
add_action('personal_options_update', 'horsetools_profile_update');
add_action('edit_user_profile_update', 'horsetools_profile_update');
// add img avatar
function horsetools_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;
    if ( is_numeric( $id_or_email ) ) {
        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );
    } elseif ( is_object( $id_or_email ) ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }
    } else {
        $user = get_user_by( 'email', $id_or_email );
    }
    if($user){
        $custom_avatar = absint( get_user_meta( $user->data->ID, 'horsetoolspic', true ) );
        // display_name is user-controlled and WordPress does not encode single
        // quotes in it, so interpolating it raw into single-quoted attributes
        // let any Subscriber break out with  x' onerror='...
        $name = get_the_author_meta('display_name', $user->data->ID);
        if( $custom_avatar ){
            $image  =   wp_get_attachment_image_src($custom_avatar, array('30' , '30'));
            if( $image ){
                $avatar = sprintf(
                    '<img title="%1$s" alt="%1$s" src="%2$s" class="avatar avatar-%3$d photo" height="%3$d" width="%3$d" loading="lazy" />',
                    esc_attr( $name ),
                    esc_url( $image[0] ),
                    (int) $size
                );
            }
        }
    }
    return $avatar;
}
add_filter( 'get_avatar' , 'horsetools_custom_avatar' , 1 , 5 );
// add url avatar
function horsetools_custom_avatar_url( $url, $id_or_email, $args) {
    $user = false;
    if ( is_numeric( $id_or_email ) ) {
        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );
    } elseif ( is_object( $id_or_email ) ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }
    } else {
        $user = get_user_by( 'email', $id_or_email );
    }
    if($user){
        $custom_avatar = absint( get_user_meta( $user->data->ID, 'horsetoolspic', true ) );
        if( $custom_avatar ){
            $image  =   wp_get_attachment_image_src($custom_avatar, array('30' , '30'));
            if( $image ){
                $url = esc_url_raw( $image[0] );
            }
        }
    }
    return $url;
}
add_filter( 'get_avatar_url' , 'horsetools_custom_avatar_url' , 10 , 3 );
}
# show id user admin user
if (isset($horsetools_options['user-id1'])){
function horsetools_modify_user_table( $column ) {
    $column['user_id'] = 'User ID';
    return $column;
}
add_filter('manage_users_columns', 'horsetools_modify_user_table');
function horsetools_display_user_id( $val, $column_name, $user_id ) {
    if ( 'user_id' === $column_name ) {
        return $user_id;
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'horsetools_display_user_id', 10, 3 );
}

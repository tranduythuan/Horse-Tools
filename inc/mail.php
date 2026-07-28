<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# cau hinh gmail smtp
if(isset($horsetools_options['mail-gsmtp1'])){
function horsetools_mail_smtp($phpmailer) {
	global $horsetools_options;
	$smtp_name = !empty($horsetools_options['mail-gsmtp11']) ? $horsetools_options['mail-gsmtp11'] : null;
	$smtp_e = !empty($horsetools_options['mail-gsmtp12']) ? $horsetools_options['mail-gsmtp12'] : null;
	$smtp_tk = !empty($horsetools_options['mail-gsmtp13']) ? $horsetools_options['mail-gsmtp13'] : null;
	$smtp_mk = !empty($horsetools_options['mail-gsmtp14']) ? $horsetools_options['mail-gsmtp14'] : null;
	$smtp_sv = !empty($horsetools_options['mail-gsmtp15']) ? $horsetools_options['mail-gsmtp15'] : null;
	$smtp_hot = !empty($horsetools_options['mail-gsmtp16']) ? $horsetools_options['mail-gsmtp16'] : null;
	$smtp_kn = !empty($horsetools_options['mail-gsmtp17']) ? $horsetools_options['mail-gsmtp17'] : null;
	if (!is_object( $phpmailer ))
	$phpmailer = (object) $phpmailer;
	$phpmailer->Mailer = 'smtp';
	if(isset($horsetools_options['mail-gsmtp18'])){
		$phpmailer->SMTPAuth = true;
	} else {
		$phpmailer->SMTPAuth = false;	
	}
	$phpmailer->FromName = $smtp_name;
	$phpmailer->From = $smtp_e;
	$phpmailer->Username = $smtp_tk;
	$phpmailer->Password = $smtp_mk;
	$phpmailer->Host = $smtp_sv;
	$phpmailer->Port = $smtp_hot;
	// Migrate the old invalid 'starttls' value on read, so sites that already
	// saved it start working again without the admin having to notice.
	if ( 'starttls' === $smtp_kn ) {
		$smtp_kn = 'tls';
	}
	$phpmailer->SMTPSecure = in_array( $smtp_kn, array( 'tls', 'ssl' ), true ) ? $smtp_kn : '';
}
add_action( 'phpmailer_init', 'horsetools_mail_smtp');
# test thu email
function horsetools_send_email() {
    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error('forbidden', 403);
    }
    if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'ht-send-email-nonce')) {
        $admin_email = get_option('admin_email');
        $to = !empty($_POST['email']) ? sanitize_email($_POST['email']) : $admin_email;
        $subject = __('Test SMTP email sending from your website', 'horse-tools');
        $message = __('If you receive this email, then the SMTP email sending function is working well', 'horse-tools');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $sent = wp_mail($to, $subject, $message, $headers);
        if ($sent) {
            echo __('Email sent successfully', 'horse-tools');
        } else {
            echo __('Unable to send email!', 'horse-tools');
        }
    }
    wp_die();
}
add_action('wp_ajax_horsetools_send_email_ajax', 'horsetools_send_email');
}
# thong bao email khi co ai tra loi nhan xet
if(isset($horsetools_options['mail-com1'])){
/**
 * Notify the parent commenter that someone replied.
 *
 * Previously this ran for every comment: on a top-level comment the parent is 0,
 * get_comment(0) returns null, and reading ->comment_author_email off null threw
 * a PHP 8 warning before calling wp_mail(null, …). It also fired for comments
 * still in moderation and for spam, and would email someone their own reply.
 */
function horsetools_send_email_on_reply( $comment_id, $comment_object ) {
	if ( ! is_object( $comment_object ) || empty( $comment_object->comment_parent ) ) {
		return;
	}
	if ( '1' !== (string) $comment_object->comment_approved ) {
		return;
	}
	$parent = get_comment( $comment_object->comment_parent );
	if ( ! $parent || empty( $parent->comment_author_email ) ) {
		return;
	}
	if ( 0 === strcasecmp( $parent->comment_author_email, (string) $comment_object->comment_author_email ) ) {
		return;
	}
  $to = $parent->comment_author_email;
  $subject = __('Your comment has just been replied to', 'horse-tools');
  $message = __('Your comment has just been replied to in the post', 'horse-tools') .' '. get_the_title( $comment_object->comment_post_ID ) . ': ' . get_comment_link( $comment_id );
  wp_mail( $to, $subject, $message );
}
add_action( 'wp_insert_comment', 'horsetools_send_email_on_reply', 10, 2 );
}
# thong bao email cho tac gia khi co binh luan moi
if(isset($horsetools_options['mail-com2'])){
function horsetools_notify_post_author_on_comment( $comment_id ) {
    $comment = get_comment( $comment_id );
    // Only for a real, approved comment — otherwise the post author is emailed
    // about every spam comment the site receives.
    if ( ! $comment || '1' !== (string) $comment->comment_approved ) {
        return;
    }
    $post = get_post( $comment->comment_post_ID );
    if ( ! $post ) {
        return;
    }
    $author = get_userdata( $post->post_author );
    if ( $author && $author->user_email !== $comment->comment_author_email ) {
        $message = sprintf(
            __( 'Hello %s. A new comment has been posted on your article "%s". You can view the comment here: %s', 'horse-tools' ),
            $author->display_name,
            $post->post_title,
            get_comment_link( $comment_id )
        );
        $subject = sprintf( __( '[%s] New comment on the article "%s"', 'horse-tools' ), get_bloginfo( 'name' ), $post->post_title );
        wp_mail( $author->user_email, $subject, $message );
    }
}
add_action( 'comment_post', 'horsetools_notify_post_author_on_comment' );
}
# gui tài khoan khi dang ky moi
if (isset($horsetools_options['mail-new1'])){
function horsetools_email_verification( $user_id ){
	global $horsetools_options;
    $email = get_userdata( $user_id )->user_email;
    $subject = !empty($horsetools_options['mail-new11']) ? $horsetools_options['mail-new11'] : __('Welcome to the website', 'horse-tools');
    $message = !empty($horsetools_options['mail-new12']) ? $horsetools_options['mail-new12'] : __('This is an email notification of your successful registration', 'horse-tools');
    $headers = 'Content-Type: text/html; charset=UTF-8';
    wp_mail( $email, $subject, $message, $headers );
}
add_action( 'user_register', 'horsetools_email_verification', 10, 1 );
}









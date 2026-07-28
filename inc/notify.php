<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $horsetools_notify_options;
# phát hiện chặn quảng cáo
if(isset($horsetools_notify_options['notify-block1'])){
function horsetools_blocker_assets(){
	if(!is_user_logged_in()){
	wp_enqueue_style( 'horseblocker', HORSETOOLS_URL . 'link/notify/horseblocker.css', array(), HORSETOOLS_VERSION);
	wp_enqueue_script( 'horseblocker', HORSETOOLS_URL . 'link/notify/horseblocker.js', array('jquery'), HORSETOOLS_VERSION);
	}
}
add_action('wp_enqueue_scripts', 'horsetools_blocker_assets');
// ads fake
function horsetools_blocker_adsfake(){
	if(!is_user_logged_in()){
	echo '<div style="display:none" id="googleads"><div class="ads ad adsbox doubleclick ad-placement carbon-ads" style="background-color:red;height:300px;width:100%;"><!-- Quang cao -->Google Ads, Facebook Ads Google Adsense<!-- Ads --></div></div>';
	}
}
// The bait markup is a <div>. Printed on wp_head it lands inside <head>, which
// forces the parser to close </head> and open <body> right there — so every
// Open Graph tag, canonical link and JSON-LD block that any other plugin prints
// at a later wp_head priority ends up in the body, where crawlers that only
// read <head> will not see it. Print it in the body instead.
if ( function_exists( 'wp_body_open' ) ) {
	add_action( 'wp_body_open', 'horsetools_blocker_adsfake' );
} else {
	add_action( 'wp_footer', 'horsetools_blocker_adsfake', 1 );
}
// add footer
function horsetools_blocker_footer(){ 
	global $horsetools_notify_options;
	if(!is_user_logged_in()){
		$title = !empty($horsetools_notify_options['notify-block12']) ? esc_html($horsetools_notify_options['notify-block12']) : __('Ad-blocker detection', 'horse-tools');
		$content = !empty($horsetools_notify_options['notify-block13']) ? wp_kses_post($horsetools_notify_options['notify-block13']) : __('Please disable ad-blocker on your browser to access and view the content', 'horse-tools');
		$botbg = !empty($horsetools_notify_options['notify-block-c1']) ? 'background:'. horsetools_css_color($horsetools_notify_options['notify-block-c1']) .';' : NULL;
		$botbor = !empty($horsetools_notify_options['notify-block-c2']) ? 'border-bottom: 6px solid '. horsetools_css_color($horsetools_notify_options['notify-block-c2']) .';' : NULL;
		$setlock = isset($horsetools_notify_options['notify-block11']);
		?>
		<div id="ht-blocker" class="ht-blocker" style="display:none">
			<div class="ht-blocker-box">
				<div class="ht-blocker-card" id="ht-blockid" data-enabled="<?php echo json_encode($setlock); ?>">
					<p class="ht-blocker-tit"><?php echo $title; ?></p>
					<div class="ht-blocker-cont"><?php echo $content; ?></div>
					<?php if(isset($horsetools_notify_options['notify-block11'])){ ?>
					<div><span style="<?php echo esc_attr($botbg . $botbor); ?>" id="ht-blocker-clo"><?php _e('Agree', 'horse-tools') ?></span></div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action('wp_footer', 'horsetools_blocker_footer');
}
# thong bao
if(isset($horsetools_notify_options['notify-notis1'])){
function horsetools_notic_footer(){ 
	global $horsetools_notify_options;
	$content = !empty($horsetools_notify_options['notify-notis11']) ? wp_kses_post($horsetools_notify_options['notify-notis11']) : __('You havent added content yet', 'horse-tools');
	$colorbg = !empty($horsetools_notify_options['notify-notis-c1']) ? 'style="background-color:'. horsetools_css_color($horsetools_notify_options['notify-notis-c1']). ';"' : NULL;
	?>
	<div class="noti-info noti-message" id="noti-message" <?php echo $colorbg; ?>>
	<div class="fix-message noti-message-box">
		<div class="noti-message-1"><?php echo $content; ?></div>
		<div class="noti-message-2"><button onclick="htnone(event, 'noti-message')">&#215;</button></div>
	</div>
	</div>
	<style> .noti-message {background-size: 40px 40px;background-image: linear-gradient(135deg, rgba(255, 255, 255, .05) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .05) 50%, rgba(255, 255, 255, .05) 75%, transparent 75%, transparent);width: 100%;border: none;color: #fff;padding: 20px 0px;position: fixed;top:0;text-shadow: 0 1px 0 rgba(0,0,0,.5);-webkit-animation: animate-ms 2s linear infinite;-moz-animation: animate-ms 2s linear infinite;z-index:9999;box-sizing: border-box;}.fix-message{max-width:1200px;margin:auto;padding: 0px 20px;}.noti-message a{color: #fff444;display: inline-block;}.noti-message-box{display:flex;}.noti-message-1{width:100%;animation: ani 1.5s;font-size: 16px;}@keyframes ani{from{filter: blur(5px);opacity: 0;}to{letter-spacing: 0;filter: blur(0);opacity: 1px;}}@keyframes thongbao-top {0% {transform: translate3d(0, 0, 0) scale(1);}33.3333% {transform: translate3d(0, 0, 0) scale(0.5);}66.6666% {transform: translate3d(0, 0, 0) scale(1);}100% {transform: translate3d(0, 0, 0) scale(1);}0% {box-shadow: 0 0 0 0px #fff,0 0 0 0px #fff;}50% {transform: scale(0.98);}100% {box-shadow: 0 0 0 15px rgba(0,210,255,0),0 0 0 30px rgba(0,210,255,0);}}.noti-message-2{width:30px;margin-left:10px;display: flex;align-items: center;}.noti-message-2 button {min-height:0px;min-width:0px;padding: 0px;width: 30px;height: 30px;display: flex;align-items: center;justify-content: center;font-size: 16px;background: #ffffff29;color: #fff;border-radius: 100%;animation: thongbao-top 1000ms infinite;margin:0px;border:none}.noti-info {background-color: #4ea5cd;}@-webkit-keyframes animate-ms {from {background-position: 0 0;}to {background-position: -80px 0;}}@-moz-keyframes animate-ms {from {background-position: 0 0;}to {background-position: -80px 0;}}</style>
	<?php
}
add_action('wp_footer', 'horsetools_notic_footer');	
}
# cookie
if(isset($horsetools_notify_options['notify-cookie1'])){
function horsetools_cookie_assets(){
	wp_enqueue_style( 'horsecookie', HORSETOOLS_URL . 'link/notify/horsecookie.css', array(), HORSETOOLS_VERSION);
	wp_enqueue_script( 'horsecookie', HORSETOOLS_URL . 'link/notify/horsecookie.js', array(), HORSETOOLS_VERSION, true);
}
add_action('wp_enqueue_scripts', 'horsetools_cookie_assets');
function horsetools_cookie_footer(){ 
	global $horsetools_notify_options;
	$title = !empty($horsetools_notify_options['notify-cookie11']) ? esc_html($horsetools_notify_options['notify-cookie11']) : __('Cookies', 'horse-tools');
	$content = !empty($horsetools_notify_options['notify-cookie12']) ? wp_kses_post($horsetools_notify_options['notify-cookie12']) : __('This website uses cookies to ensure you get the best experience on our site. By continuing to browse, you agree to our use of cookies. For more information, please read our Cookie Policy', 'horse-tools');
	$colortit = !empty($horsetools_notify_options['notify-cookie-c1']) ? '--cookiecolor:'. horsetools_css_color($horsetools_notify_options['notify-cookie-c1']) .';' : NULL;
	$right = isset($horsetools_notify_options['notify-cookie-c2']) && $horsetools_notify_options['notify-cookie-c2'] == 'Right' ? '.ht-cookie{right:10px;left:unset;}' : NULL;
	$link = !empty($horsetools_notify_options['notify-cookie-l1']) ? esc_url($horsetools_notify_options['notify-cookie-l1']) : 'javascript:void(0)';
	?>
	<div id="ht-cookie" class="ht-cookie">
		<div class="ht-cookie-tit">
			<span class="ht-cookie-text"><svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24"><path fill="currentColor" d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11c-1.654 0-3-1.346-3.003-2.937c.005-.034.016-.136.017-.17a.998.998 0 0 0-1.254-1.006A2.963 2.963 0 0 1 15 7c-1.654 0-3-1.346-3-3c0-.217.031-.444.099-.716a1 1 0 0 0-1.067-1.236A9.956 9.956 0 0 0 2 12c0 5.514 4.486 10 10 10s10-4.486 10-10c0-.049-.003-.097-.007-.16a1.004 1.004 0 0 0-.395-.776zM12 20c-4.411 0-8-3.589-8-8a7.962 7.962 0 0 1 6.006-7.75A5.006 5.006 0 0 0 15 9l.101-.001a5.007 5.007 0 0 0 4.837 4C19.444 16.941 16.073 20 12 20z"/><circle cx="12.5" cy="11.5" r="1.5" fill="currentColor"/><circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/><circle cx="7.5" cy="12.5" r="1.5" fill="currentColor"/><circle cx="15.5" cy="15.5" r="1.5" fill="currentColor"/><circle cx="10.5" cy="16.5" r="1.5" fill="currentColor"/></svg> <?php echo $title; ?></span>
			<span class="ht-cookie-close">&#215;</span>
		</div>
		<span class="ht-cookie-content"><?php echo $content; ?></span>
		<div class="ht-cookie-but">
			<a title="<?php _e('Policy', 'horse-tools'); ?>" href="<?php echo $link; ?>" target="_blank"><?php _e('Policy', 'horse-tools'); ?></a>
			<a title="<?php _e('Agree', 'horse-tools'); ?>" class="ht-cookie-oke" href="javascript:void(0)"><?php _e('Agree', 'horse-tools'); ?></a>
		</div>
	</div>
	<?php
	echo '<style>:root{'. $colortit .'}'. $right .'</style>';
}
add_action('wp_footer', 'horsetools_cookie_footer');	
}  
#popup
if(isset($horsetools_notify_options['notify-popup1'])){
function horsetools_popup_assets(){
	wp_enqueue_script( 'horsepopup', HORSETOOLS_URL . 'link/notify/horsepopup.js', array('jquery'), HORSETOOLS_VERSION, true);
	wp_enqueue_style( 'horsepopup', HORSETOOLS_URL . 'link/notify/horsepopup.css', array(), HORSETOOLS_VERSION);
}
add_action('wp_enqueue_scripts', 'horsetools_popup_assets');
function horsetools_popup_footer(){ 
	global $horsetools_notify_options;
	$time = !empty($horsetools_notify_options['notify-popup-c1']) ? horsetools_css_number($horsetools_notify_options['notify-popup-c1']) : NULL;
	$title1 = !empty($horsetools_notify_options['notify-popup12']) ? '<div class="ht-popupnew-tit">'. esc_html($horsetools_notify_options['notify-popup12']) .'</div>' : NULL;
	$title2 = !empty($horsetools_notify_options['notify-popup12']) ? esc_attr($horsetools_notify_options['notify-popup12']) : __('Hello', 'horse-tools');
	$images = !empty($horsetools_notify_options['notify-popup11']) ? '<img alt="'. esc_attr($title2) .'" src="'. esc_url($horsetools_notify_options['notify-popup11']) .'" />' : NULL;
	$content = !empty($horsetools_notify_options['notify-popup13']) ? do_shortcode(wp_kses_post($horsetools_notify_options['notify-popup13'])) : NULL;
	$link1 = !empty($horsetools_notify_options['notify-popup14']) ? '<a title="'. esc_attr($title2) .'" href="'. esc_url($horsetools_notify_options['notify-popup14']) .'">'. $images .'</a>' : $images;
	
	$borderr = !empty($horsetools_notify_options['notify-popup-m1']) ? horsetools_css_number($horsetools_notify_options['notify-popup-m1']) .'px' : '0px';
	$maxwidth = !empty($horsetools_notify_options['notify-popup-m2']) ? '.ht-modal{max-width:'. horsetools_css_number($horsetools_notify_options['notify-popup-m2']) .'px;}' : NULL;
	// skin 2
	if (!empty($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '2'){ ?>
	<div id="htpopupex" class="ht-modal ht-modal-skin2">
		<div class="ht-popupnew-content">
			<?php echo $title1; ?>
			<div class="ht-popupnew-img"><?php echo $link1; ?></div>
			<div class="ht-popupnew-tex"><?php echo $content; ?></div>
		</div>
	</div>
	<style><?php echo $maxwidth; ?>.ht-modal{border-radius:<?php echo $borderr; ?>;}.ht-modal-skin2 .ht-popupnew-img img{border-radius:calc(<?php echo $borderr ?> - 2px);}</style>
	
	<?php 
	// skin 3
	} else if (!empty($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '3'){ ?>
	<div id="htpopupex" class="ht-modal ht-modal-skin3">
		<div class="ht-popupnew">
			<div class="ht-popupnew-img"><?php echo $link1; ?></div>
			<div class="ht-popupnew-content">
				<?php echo $title1; ?>
				<div class="ht-popupnew-tex"><?php echo $content; ?></div>
			</div>
		</div>
	</div>
	<style><?php echo $maxwidth; ?>.ht-modal{border-radius:<?php echo $borderr; ?>;}.ht-modal-skin3 .ht-popupnew-img img{border-radius: <?php echo $borderr .' '. $borderr .' 0px 0px'; ?>;}</style>
	<?php
	// skin 4
	} else if (!empty($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '4'){ ?>
	<div id="htpopupex" class="ht-modal ht-modal-skin4">
		<div class="ht-popupnew-img"><?php echo $link1; ?></div>
	</div>
	<style><?php echo $maxwidth; ?>.ht-modal-skin4 .ht-popupnew-img img{border-radius: <?php echo $borderr; ?>;}</style>
	
	<?php
	// skin 1
	} else { ?>
	<div id="htpopupex" class="ht-modal ht-modal-skin1">
		<div class="ht-popupnew">
			<div class="ht-popupnew-img"><?php echo $link1; ?></div>
			<div class="ht-popupnew-content">
				<?php echo $title1; ?>
				<div class="ht-popupnew-tex"><?php echo $content; ?></div>
			</div>
		</div>
	</div>
	<style><?php echo $maxwidth; ?>.ht-modal{border-radius:<?php echo $borderr; ?>;}.ht-modal-skin1 .ht-popupnew-img img{border-radius:<?php echo $borderr .' 0px 0px '. $borderr; ?>;} @media(max-width:700px){.ht-modal-skin1 .ht-popupnew-img img{border-radius: <?php echo $borderr .' '. $borderr .' 0px 0px'; ?>;}}</style>
	
	<?php } ?>
	<span id="popup-timer" data-time="<?php echo esc_attr($time); ?>"></span>
	<?php
}
add_action('wp_footer', 'horsetools_popup_footer');
}



<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Horse Tools — the Overview screen.
 *
 * This used to be the plugin: one screen with thirteen tabs, every setting in
 * it, and 546 KB of HTML of which the twelve tabs you were not looking at made
 * up 260 KB. The settings now live on screens named after the job they do, so
 * what belongs here is a way in — what is on, what needs attention, and where
 * everything went.
 */

/**
 * The grouped screens, in menu order.
 *
 * @return array<string,array{title:string,icon:string,desc:string}>
 */
function horsetools_groups() {
	return array(
		'horsetools-speed-options'     => array(
			'title' => __( 'Speed', 'horse-tools' ),
			'icon'  => 'ti-gauge',
			'desc'  => __( 'Scripts, styles, lazy loading, image compression and WebP.', 'horse-tools' ),
		),
		'horsetools-seo-options'       => array(
			'title' => __( 'SEO', 'horse-tools' ),
			'icon'  => 'ti-chart-arrows-vertical',
			'desc'  => __( 'Permalinks, image alt text, external links and FAQ schema.', 'horse-tools' ),
		),
		'horsetools-security-options'  => array(
			'title' => __( 'Security', 'horse-tools' ),
			'icon'  => 'ti-shield-half',
			'desc'  => __( 'Login lockout, two-factor, reCAPTCHA and the login page.', 'horse-tools' ),
		),
		'horsetools-content-options'   => array(
			'title' => __( 'Content', 'horse-tools' ),
			'icon'  => 'ti-notes',
			'desc'  => __( 'Post images, duplicating posts and the image lightbox.', 'horse-tools' ),
		),
		'horsetools-display-options'   => array(
			'title' => __( 'Appearance', 'horse-tools' ),
			'icon'  => 'ti-device-desktop',
			'desc'  => __( 'Dark mode, scrollbar, effects, and the look of the admin area.', 'horse-tools' ),
		),
		'horsetools-customers-options' => array(
			'title' => __( 'Customers', 'horse-tools' ),
			'icon'  => 'ti-message',
			'desc'  => __( 'Chat buttons, contact channels and WooCommerce tweaks.', 'horse-tools' ),
		),
		'horsetools-accounts-options'  => array(
			'title' => __( 'Accounts & Email', 'horse-tools' ),
			'icon'  => 'ti-user',
			'desc'  => __( 'Roles, avatars, SMTP and signing in with Google.', 'horse-tools' ),
		),
		'horsetools-tools-options'     => array(
			'title' => __( 'Tools', 'horse-tools' ),
			'icon'  => 'ti-tools',
			'desc'  => __( 'Admin housekeeping and the plugin\'s own settings.', 'horse-tools' ),
		),
	);
}

function horsetools_options_page() {
	global $horsetools_options;
	ob_start();
	?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top"></div>
	<div class="ht-wrap2">
	  <div class="ht-box">

		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://tranduythuan.com/" target="_blank">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="httab(event, 'ov-tab1')"><i class="ti ti-adjustments"></i> <?php _e( 'Overview', 'horse-tools' ); ?></button>
		</div>

		<div class="ht-main">
			<div class="sotab-box htbox" id="ov-tab1">
				<div class="ht-card">
					<h2><?php _e( 'Overview', 'horse-tools' ); ?></h2>
					<p class="ht-note"><i class="ti ti-bulb"></i>
						<?php _e( 'Settings are grouped by what they do. Pick a group below, or search for a setting by name in the panel on the right.', 'horse-tools' ); ?>
					</p>
					<div class="ht-grid-cards">
						<?php foreach ( horsetools_groups() as $slug => $g ) : ?>
						<a class="ht-gcard" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>">
							<i class="ti <?php echo esc_attr( $g['icon'] ); ?>" aria-hidden="true"></i>
							<strong><?php echo esc_html( $g['title'] ); ?></strong>
							<span><?php echo esc_html( $g['desc'] ); ?></span>
						</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

	  </div>
	  <div class="ht-sidebar">
		<?php if ( function_exists( 'horsetools_health_card' ) ) { horsetools_health_card(); } ?>
	  </div>
	</div>
	</div>
	<?php
	require_once( HORSETOOLS_DIR . 'main/style.php' );
	echo ob_get_clean();
}
function horsetools_tool_add_options_link() {
	global $horsetools_options;
	$icon = horsetools_icon();
	$name = !empty($horsetools_options['horsetools6']) ? $horsetools_options['horsetools6'] : 'Horse Tools';
	add_menu_page($name, $name, 'manage_options', 'horsetools-options', 'horsetools_options_page', $icon, 70);
	add_submenu_page('horsetools-options', $name, '<i class="ti ti-adjustments" style="width:20px;"></i> '. __('Overview', 'horse-tools'), 'manage_options', 'horsetools-options', '', 1);
}
add_action('admin_menu', 'horsetools_tool_add_options_link');
function horsetools_tool_register_settings() {
	register_setting( 'horsetools_settings_group', 'horsetools_settings', array( 'sanitize_callback' => 'horsetools_sanitize_main' ) );
}
add_action('admin_init', 'horsetools_tool_register_settings');
// clear cache
function horsetools_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_settings', 'options');
}
add_action('update_option_horsetools_settings', 'horsetools_settings_cache', 10, 2);

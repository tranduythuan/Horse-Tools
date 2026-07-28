<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('ABOUT', 'horse-tools'); ?></h2>
  <h3><i class="fa-regular fa-star"></i> <?php _e('Information about your website', 'horse-tools') ?></h3>
	<div class="ht-card-note"> 
	<p><?php $theme = wp_get_theme(); echo 'Theme: ' . esc_html($theme->Name) .' <b>'. esc_html($theme->Version) .'</b>'; ?></p>
	<p><?php $horsetools = HORSETOOLS_VERSION; echo __('Horse Tools:', 'horse-tools'). ' <b>'. esc_html($horsetools) .'</b>'; ?></p>
	<p>WordPress: <b><?php echo esc_html(get_bloginfo('version')); ?></b></p>
	<p>Lang: <b><?php echo esc_html($lang=get_bloginfo("language")); ?></b></p>
	<p>PHP: <b><?php echo esc_html(phpversion()); ?></b></p>
	<p><?php horsetools_display_db_info(); ?></p>
	<p><?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?></p>
	<p>
	<?php
	$active_plugins = get_option('active_plugins');
	echo 'Plugin: ';
	foreach ($active_plugins as $plugin_path) {
		$plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
		echo '<b>'. esc_html($plugin_info['Name']) . '</b>, ';
	}
	?>
	</p>
	<p>
	<?php
	$admin_users = get_users(array(
		'role' => 'administrator',
	));
	if (!empty($admin_users)) {
		echo 'User Admin: ';
		foreach ($admin_users as $admin_user) {
			echo '<b>' . esc_html($admin_user->user_nicename) . '</b>, ';
		}
	} 
	?>
	</p>
	<p>
	<?php
	$user_count = count_users();
	echo 'User: ' . esc_html($user_count['total_users']);
	?>
	</p>
	<p>
	<?php
	// check gd
	if (extension_loaded('gd') || extension_loaded('gd2')) {
		echo __('The GD library has been installed', 'horse-tools');
	} else {
		echo __('The GD library has not been installed', 'horse-tools');
	}
	?>
	</p>
	</div>
  <h3><i class="fa-regular fa-star"></i> <?php _e('Plugin development', 'horse-tools') ?></h3>
	<div class="ht-card-note">
	<p><?php _e('Author and maintainer:', 'horse-tools') ?> <b>Trần Duy Thuận</b></p>
	<p><?php _e('Licence:', 'horse-tools') ?> <b>GPLv2 or later</b></p>
	<p><?php
		/* translators: %s: name of the original plugin and its author. */
		printf(
			esc_html__( 'Horse Tools is built on %s, which is no longer maintained by its author. That original work is gratefully acknowledged and this fork continues under the same GPLv2 licence.', 'horse-tools' ),
			'<b>' . esc_html__( 'Foxtool by Fox Theme (Nguyễn Ngọc Hoàn)', 'horse-tools' ) . '</b>'
		);
	?></p>
	<p><?php _e('Original contributors:', 'horse-tools') ?> <b>Vu Ngoc Tuan, AR T RU, Linh Tran, Di Hu Hoa Tung, Minh Nhut</b></p>
	</div>
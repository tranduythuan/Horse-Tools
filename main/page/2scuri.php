<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('SECURITY', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check2" data-target="play2" type="checkbox" name="horsetools_settings[scuri]" value="1" <?php if ( isset($horsetools_options['scuri']) && 1 == $horsetools_options['scuri'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play2" class="ht-card toggle-div">
  <h3><i class="fa-regular fa-badge-check"></i> <?php _e('Enhance website security', 'horse-tools') ?></h3>
	<!-- scuri off 1 -->
	<?php horsetools_toggle( 'scuri-off1', __( 'Disable REST API', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Enhance website security',
	) ); ?>
	<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you not using REST API, it recommended to disable it for security purposes', 'horse-tools'); ?></p>
	<!-- scuri off 2 -->
	<?php horsetools_toggle( 'scuri-off2', __( 'Disable XML RPC', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Enhance website security',
		'description' => __( 'If you not using XML RPC, it recommended to disable it for security purposes', 'horse-tools' ),
	) ); ?>
	<!-- scuri off 3 -->
	<?php horsetools_toggle( 'scuri-off3', __( 'Disable Wp-Embed', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Enhance website security',
		'description' => __( 'If you not using Wp-Embed, it recommended to disable it for security purposes', 'horse-tools' ),
	) ); ?>
	<!-- scuri off 4 -->
	<?php horsetools_toggle( 'scuri-off4', __( 'Disable X-Pingback', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Enhance website security',
		'description' => __( 'If you not using X-Pingback, it recommended to disable it for security purposes', 'horse-tools' ),
	) ); ?>
	<!-- scuri off 5 -->
	<?php horsetools_toggle( 'scuri-off5', __( 'Remove unnecessary header information', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Enhance website security',
		'description' => __( 'Remove unnecessary header information if desired', 'horse-tools' ),
	) ); ?>
	<!-- scuri off 6 -->
	<?php horsetools_toggle( 'scuri-off6', __( 'Disable other data sources', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Enhance website security',
		'description' => __( 'Disable unnecessary data sources', 'horse-tools' ),
	) ); ?>


  <h3><i class="fa-regular fa-badge-check"></i> <?php _e('Filter uploaded files', 'horse-tools') ?></h3>
	<!-- scuri off 1 -->
	<?php horsetools_toggle( 'scuri-up1', __( 'Enable blocking uploads of non-image files', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Filter uploaded files',
		'description' => __( 'This feature will block uploads of all files that are not image formats, from media, plugins, themes, etc', 'horse-tools' ),
	) ); ?>

  <h3><i class="fa-regular fa-badge-check"></i> <?php _e('Remove version', 'horse-tools') ?></h3>
	<!-- scuri ver off 1 -->
	<?php horsetools_toggle( 'scuri-verof1', __( 'Remove version from JS and CSS', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Remove version',
	) ); ?>
	<!-- scuri ver off 2 -->
	<?php horsetools_toggle( 'scuri-verof2', __( 'Remove WordPress version', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Remove version',
	) ); ?>

	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Correct, hiding the version of resources such as JS, CSS, and WordPress is a common security measure to prevent hackers from exploiting known vulnerabilities in older versions', 'horse-tools'); ?></p>

  <h3><i class="fa-regular fa-badge-check"></i> <?php _e('Secure access data', 'horse-tools') ?></h3>
	<!-- SQL injection -->
	<?php horsetools_toggle( 'scuri-sql1', __( 'Prevent SQL injection, cross-site scripting (XSS)', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Secure access data',
		'description' => __( 'This feature helps protect the website against attacks such as SQL injection, cross-site scripting (XSS)', 'horse-tools' ),
	) ); ?>
</div>

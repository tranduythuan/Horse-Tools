<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('OPTIMIZE', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check1" data-target="play1" type="checkbox" name="horsetools_settings[speed]" value="1" <?php if ( isset($horsetools_options['speed']) && 1 == $horsetools_options['speed'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play1" class="toggle-div ht-card">
  <h3><i class="fa-regular fa-square-minus"></i> <?php _e('Disable unnecessary items', 'horse-tools') ?></h3>
	<!-- tôi ưu 1 -->
	<?php horsetools_toggle( 'speed-off1', __( 'Disable jQuery Migrate', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'jQuery Migrate is a library used to maintain the operation of certain themes, plugins that rely on older code. If your website no longer relies on this library, you can disable it', 'horse-tools' ),
	) ); ?>
	<!-- tôi ưu 2 -->
	<?php horsetools_toggle( 'speed-off2', __( 'Disable Gutenberg CSS', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'If you not using it, you can disable Gutenberg CSS on the homepage', 'horse-tools' ),
	) ); ?>
	<!-- tôi ưu 3 -->
	<?php horsetools_toggle( 'speed-off3', __( 'Disable Classic CSS', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'If you not using it, you can disable Classic CSS on the homepage', 'horse-tools' ),
	) ); ?>
	<!-- tôi ưu 4 -->
	<?php horsetools_toggle( 'speed-off4', __( 'Disable Emoji', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'If you not using it, you can disable Emoji', 'horse-tools' ),
	) ); ?>

  <h3><i class="fa-brands fa-square-js"></i> <?php _e('Optimization Library', 'horse-tools') ?></h3>
	<!-- thư vien js 1 -->
	<?php horsetools_toggle( 'speed-link1', __( 'Enable Instant-page', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimization Library',
		'description' => __( 'Instant-page is a library that allows you to preload the content of a linked page into the browser memory simply by hovering over the link. When you click on the link, it provides a remarkably fast loading experience', 'horse-tools' ),
	) ); ?>
	<!-- thư vien js 2 -->
	<?php horsetools_toggle( 'speed-link2', __( 'Enable Smooth-scroll', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimization Library',
		'description' => __( 'Smooth-scroll is a library that enables you to create a smooth scrolling effect, providing users with a perception of faster page navigation', 'horse-tools' ),
	) ); ?>

  <h3><i class="fa-regular fa-loader"></i> <?php _e('The function of lazy loading images', 'horse-tools') ?></h3>
	<!-- lazyload img 1 -->
	<?php horsetools_toggle( 'speed-lazy1', __( 'Enable image lazy loading', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'The function of lazy loading images',
		'description' => __( 'If you want to lazy load images every time the page loads, then turn it on. This function helps your website load faster', 'horse-tools' ),
	) ); ?>

  <h3><i class="fa-regular fa-file-zipper"></i> <?php _e('Compress HTML into a single line', 'horse-tools') ?></h3>
	<!-- nén 1 -->
	<?php horsetools_toggle( 'speed-zip1', __( 'Enable HTML compression', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Compress HTML into a single line',
		'description' => __( 'With this feature, HTML will be compressed into a single line, removing unnecessary characters and whitespace to speed up page loading', 'horse-tools' ),
		'warning'     => __( 'Do not enable if you are using optimization plugins with similar functionality (conflict)', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'speed-zip11', __( 'Minify Inline JavaScript', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip12', __( 'Remove comments from HTML, JavaScript, and CSS', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip13', __( 'Remove XHTML closing tags from empty elements in HTML5', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip14', __( 'Remove relative domain from internal URLs', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip15', __( 'Remove protocols (HTTP: and HTTPS:) from all URLs', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip16', __( 'Support multi-byte UTF-8 encoding (if you see strange characters)', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>


  <h3><i class="fa-regular fa-database"></i> <?php _e('Optimize saving post content into the database', 'horse-tools') ?></h3>
	<!-- csdl 1 -->
	<?php horsetools_toggle( 'speed-data1', __( 'Enable revision limit', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Optimize saving post content into the database',
	) ); ?>

	<?php horsetools_input( 'speed-data11', __( 'Enter the number of revisions', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimize saving post content into the database',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '3',
		'parent'      => 'speed-data1',
	) ); ?>

	<!-- csdl 2 -->
	<?php horsetools_toggle( 'speed-data2', __( 'Change save interval', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Optimize saving post content into the database',
	) ); ?>

	<?php horsetools_input( 'speed-data21', __( 'Save interval (minutes)', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimize saving post content into the database',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '1',
		'parent'      => 'speed-data2',
	) ); ?>

	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you enable this feature and set automatic revision limit and automatic save time for posts or pages, it will reduce the amount of data stored in the database', 'horse-tools'); ?></p>
</div>

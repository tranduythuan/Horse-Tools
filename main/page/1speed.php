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
  <h3><i class="ti ti-square-minus"></i> <?php _e('Disable unnecessary items', 'horse-tools') ?></h3>
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
		<!-- dashicons -->
		<?php horsetools_toggle( 'speed-dash1', __( 'Disable Dashicons for visitors', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'Disable unnecessary items',
			'description' => __( 'Removes the admin icon font (Dashicons) on the front-end for logged-out visitors, who never see it. It is kept for logged-in users because the admin bar uses it.', 'horse-tools' ),
		) ); ?>
		<!-- heartbeat -->
		<?php horsetools_toggle( 'speed-hb1', __( 'Control the Heartbeat API', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'Disable unnecessary items',
			'description' => __( 'WordPress “Heartbeat” pings the server every 15–60 seconds (autosave, post-lock, dashboard). Slowing or limiting it cuts admin-ajax.php load, especially with several admin tabs open.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field" data-ht-parent="ht-main-speed-hb1">
		<label class="ht-field-label"><?php _e('Heartbeat mode', 'horse-tools'); ?></label>
		<select name="horsetools_settings[speed-hb2]">
			<?php
			$ht_hb = ! empty( $horsetools_options['speed-hb2'] ) ? $horsetools_options['speed-hb2'] : 'slow';
			$ht_hb_opts = array(
				'slow'     => __( 'Slow down to 60 seconds (safe)', 'horse-tools' ),
				'frontend' => __( 'Disable on the front-end, slow in admin', 'horse-tools' ),
				'minimal'  => __( 'Only in the post editor (autosave/locking)', 'horse-tools' ),
			);
			foreach ( $ht_hb_opts as $ht_v => $ht_l ) {
				echo '<option value="' . esc_attr( $ht_v ) . '"' . selected( $ht_hb, $ht_v, false ) . '>' . esc_html( $ht_l ) . '</option>';
			}
			?>
		</select>
		</p>

  <h3><i class="ti ti-brand-javascript"></i> <?php _e('Optimization Library', 'horse-tools') ?></h3>
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

  <h3><i class="ti ti-bolt"></i> <?php _e('JavaScript &amp; connections', 'horse-tools') ?></h3>
		<!-- defer js -->
		<?php horsetools_toggle( 'speed-defer1', __( 'Defer JavaScript', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'JavaScript & connections',
			'description' => __( 'Add “defer” to front-end scripts so they no longer block the page from rendering; they run in order once the HTML is parsed. jQuery is never deferred (inline snippets depend on it). Big Core Web Vitals win.', 'horse-tools' ),
			'warning'     => __( 'If a theme/plugin script misbehaves, add its handle or file name to the exclusion list below. Disable if you already use a full-page optimiser that defers scripts.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field">
		<label class="ht-field-label"><?php _e('Scripts to exclude from defer (one per line — a script handle or part of its URL)', 'horse-tools'); ?></label>
		<textarea style="height:80px;" class="ht-code-textarea" name="horsetools_settings[speed-defer-exclude]" placeholder="jquery-migrate&#10;slider.min.js"><?php if(!empty($horsetools_options['speed-defer-exclude'])){echo esc_textarea($horsetools_options['speed-defer-exclude']);} ?></textarea>
		</p>

		<!-- preconnect -->
		<?php horsetools_toggle( 'speed-pre1', __( 'Preconnect to third-party hosts', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'JavaScript & connections',
			'description' => __( 'Tell the browser to start the DNS + TCP + TLS handshake to external hosts early (fonts, CDN, analytics), so their files arrive sooner. Adds preconnect and dns-prefetch hints to the page head.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field">
		<label class="ht-field-label"><?php _e('Hosts to preconnect (one per line — host or full URL)', 'horse-tools'); ?></label>
		<textarea style="height:80px;" class="ht-code-textarea" name="horsetools_settings[speed-pre-hosts]" placeholder="fonts.googleapis.com&#10;fonts.gstatic.com"><?php if(!empty($horsetools_options['speed-pre-hosts'])){echo esc_textarea($horsetools_options['speed-pre-hosts']);} ?></textarea>
		</p>
		<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Only add hosts the page really uses. Preconnecting to a host you do not load from wastes a connection.', 'horse-tools'); ?></p>

		<!-- preload -->
		<?php horsetools_toggle( 'speed-preload1', __( 'Preload critical assets', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'JavaScript & connections',
			'description' => __( 'Start fetching a few important files immediately (the LCP image, a web font, the main CSS). The type is detected from the file extension. Do not preload many files — it competes with everything else for bandwidth.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field">
		<label class="ht-field-label"><?php _e('Asset URLs to preload (one per line — .woff2/.css/.js/.jpg/.webp…)', 'horse-tools'); ?></label>
		<textarea style="height:80px;" class="ht-code-textarea" name="horsetools_settings[speed-preload-urls]" placeholder="https://example.com/wp-content/uploads/hero.webp&#10;https://example.com/fonts/main.woff2"><?php if(!empty($horsetools_options['speed-preload-urls'])){echo esc_textarea($horsetools_options['speed-preload-urls']);} ?></textarea>
		</p>

  <h3><i class="ti ti-loader"></i> <?php _e('The function of lazy loading images', 'horse-tools') ?></h3>
	<!-- lazyload img 1 -->
	<?php horsetools_toggle( 'speed-lazy1', __( 'Native image lazy-load + async decode', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'The function of lazy loading images',
		'description' => __( 'Adds decoding="async" to images so the browser decodes them off the main thread, and relies on WordPress’ built-in native lazy-load (which correctly keeps the first/LCP image eager). Replaces the old script-based method that removed image src — that hurt SEO and broke images with JavaScript off.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-file-zip"></i> <?php _e('Compress HTML into a single line', 'horse-tools') ?></h3>
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


  <h3><i class="ti ti-database"></i> <?php _e('Optimize saving post content into the database', 'horse-tools') ?></h3>
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

	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you enable this feature and set automatic revision limit and automatic save time for posts or pages, it will reduce the amount of data stored in the database', 'horse-tools'); ?></p>
</div>

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

  <h3><i class="ti ti-user-shield"></i> <?php _e('Limit login attempts', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-login1', __( 'Lock out repeated failed logins', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Limit login attempts',
		'description' => __( 'After too many failed logins from the same address, block further attempts for a while. This is the real defence against password guessing.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_input( 'scuri-login-max', __( 'Attempts before lockout', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Limit login attempts',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '5',
		'min'         => '1',
		'max'         => '50',
		'parent'      => 'scuri-login1',
	) ); ?>
	<?php horsetools_input( 'scuri-login-mins', __( 'Lockout length (minutes)', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Limit login attempts',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '15',
		'min'         => '1',
		'max'         => '1440',
		'parent'      => 'scuri-login1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-login-mail', __( 'Email me when an address is locked out', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Limit login attempts',
		'parent'  => 'scuri-login1',
	) ); ?>

  <h3><i class="ti ti-user-question"></i> <?php _e('Block user enumeration', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-enum1', __( 'Hide usernames from scanners', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Block user enumeration',
		'description' => __( 'Blocks ?author=N scans, removes the users REST endpoint for anonymous requests, strips the author from oEmbed, and makes login errors generic so they do not reveal whether the username or the password was wrong.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-shield-half"></i> <?php _e('Security response headers', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-head1', __( 'Send security headers', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Security response headers',
		'description' => __( 'Add hardening headers to front-end responses. Each one below is optional.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-xfo', __( 'X-Frame-Options: SAMEORIGIN (block clickjacking)', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-nosniff', __( 'X-Content-Type-Options: nosniff', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-ref', __( 'Referrer-Policy: strict-origin-when-cross-origin', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-perm', __( 'Permissions-Policy: block geolocation, mic and camera', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-hsts', __( 'HSTS (force HTTPS for 180 days)', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Security response headers',
		'parent'  => 'scuri-head1',
		'warning' => __( 'Only enable once HTTPS works everywhere. Browsers will refuse plain HTTP to your site for six months, and it cannot be undone quickly.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_input( 'scuri-head-csp', __( 'Content-Security-Policy (advanced, leave blank if unsure)', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Security response headers',
		'parent'      => 'scuri-head1',
		'placeholder' => "default-src 'self'",
		'description' => __( 'A wrong CSP silently breaks scripts, styles and images. Test with browser dev tools before relying on it.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-shield-lock"></i> <?php _e('Lock down the admin', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-fileedit1', __( 'Disable the theme & plugin file editor', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Lock down the admin',
		'description' => __( 'Removes the built-in code editor under Appearance and Plugins. If an attacker gets into wp-admin, they cannot use it to edit PHP files. You edit files over SFTP instead.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-rosette-discount-check"></i> <?php _e('Disable unused endpoints', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-off1', __( 'Disable REST API for anonymous visitors', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Disable unused endpoints',
		'warning' => __( 'This blocks the REST API for logged-out visitors. It WILL break: WooCommerce cart and checkout for guests, Contact Form 7 and other REST-based forms, comment submission on block themes, and oEmbed. Only enable it if your site uses none of these.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off2', __( 'Disable XML RPC', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Recommended. xmlrpc.php is a common brute-force and pingback-amplification target and almost nothing uses it now (except Jetpack).', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off3', __( 'Disable Wp-Embed', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Removes wp-embed.js if you do not embed other WordPress posts.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off4', __( 'Disable X-Pingback', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Removes the X-Pingback header. Pairs with disabling XML-RPC.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off6', __( 'Disable feeds (RSS/Atom)', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Turns off the RSS and Atom feeds if your site does not publish one.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-wash"></i> <?php _e('Tidy up', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-off5', __( 'Remove unnecessary header tags', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Tidy up',
		'description' => __( 'Removes the RSD, WLW manifest and adjacent-post link tags from the page head.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-verof2', __( 'Remove the WordPress version tag', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Tidy up',
		'description' => __( 'Removes the generator meta tag. A small tidy-up — not a security measure on its own, since asset fingerprints reveal the version anyway.', 'horse-tools' ),
	) ); ?>
</div>

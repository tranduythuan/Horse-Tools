<?php
/**
 * Horse Tools — the Security screen.
 *
 * Protecting the login was split across three unrelated tabs: the lockout,
 * security question and two-factor settings under SECURITY, the reCAPTCHA
 * check under GOOGLE because it happens to be a Google product, and moving or
 * restyling the login page under CUSTOM. Someone hardening their login had to
 * know to visit all three. They are one job, so they are now one screen.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_security_page() {
	horsetools_group_render( array(
		'sec-tab1' => array(
			'label' => __( 'Protection', 'horse-tools' ),
			'icon'  => 'ti-shield-half',
			'files' => array( 'main/page/2scuri.php' ),
		),
		'sec-tab2' => array(
			'label' => __( 'Login page', 'horse-tools' ),
			'icon'  => 'ti-login',
			'files' => array( 'main/section/sec-login.php', 'main/section/sec-recaptcha.php' ),
		),
		'sec-tab3' => array(
			'module' => 'redirect',
			'label' => __( 'Maintenance 503', 'horse-tools' ),
			'icon'  => 'ti-bug',
			'files' => array( 'main/section/sec-503.php' ),
		),
	) );
}

function horsetools_security_menu() {
	horsetools_group_menu( 'security-options', __( 'Security', 'horse-tools' ), 'ti-shield-half', 'horsetools_security_page', 4 );
}
add_action( 'admin_menu', 'horsetools_security_menu', 20 );

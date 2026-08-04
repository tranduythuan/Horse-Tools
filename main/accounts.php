<?php
/**
 * Horse Tools — the Accounts & Email screen.
 *
 * Who can get in, and how the site talks to them. Google sign-in stayed here rather than under Security: it is a way for real people to log in, not a defence against attackers — reCAPTCHA is the defence, and that moved to Security.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_accounts_page() {
	horsetools_group_render( array(
		'ac-tab1' => array(
			'label' => __( 'Users', 'horse-tools' ),
			'icon'  => 'ti-user',
			'files' => array( 'main/page/9user.php' ),
		),
		'ac-tab2' => array(
			'label' => __( 'Email', 'horse-tools' ),
			'icon'  => 'ti-mail',
			'files' => array( 'main/page/7mail.php' ),
		),
		'ac-tab3' => array(
			'label' => __( 'Google sign-in', 'horse-tools' ),
			'icon'  => 'ti-brand-google',
			'files' => array( 'main/page/11google.php' ),
		),	) );
}

function horsetools_accounts_menu() {
	horsetools_group_menu( 'accounts-options', __( 'Accounts & Email', 'horse-tools' ), 'ti-user', 'horsetools_accounts_page', 8 );
}
add_action( 'admin_menu', 'horsetools_accounts_menu', 20 );

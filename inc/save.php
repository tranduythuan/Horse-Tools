<?php
/**
 * Horse Tools — saving a screen that spans more than one option.
 *
 * WordPress's Settings API binds a form to exactly one option: settings_fields()
 * writes an `option_page` and options.php writes that one option. That is fine
 * while every screen maps to one option, and it stops being fine the moment
 * screens are grouped by subject. An SEO screen holds the FAQ schema settings
 * (part of the shared blob) next to redirects, index-now and the table of
 * contents, each of which has its own option — under the Settings API that is
 * four separate forms and four Save buttons on one screen.
 *
 * So saving goes through admin-post.php instead: one form, one button, any
 * number of options. Each option still declares its scope, so a screen only
 * ever rewrites the keys it actually rendered (see horsetools_scope_merge()).
 *
 * register_setting() stays in place. Its sanitize callback still runs from
 * update_option() here, and the screens that have not moved across still post
 * to options.php as before.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** The admin-post action name, also used as the nonce action. */
const HORSETOOLS_SAVE_ACTION = 'horsetools_save';

/** Where a scoped settings form posts to. */
function horsetools_save_url() {
	return admin_url( 'admin-post.php' );
}

/**
 * The hidden fields a scoped settings form needs: the action, a nonce, and
 * where to come back to.
 */
function horsetools_save_fields() {
	printf(
		'<input type="hidden" name="action" value="%s">' . "\n",
		esc_attr( HORSETOOLS_SAVE_ACTION )
	);
	wp_nonce_field( HORSETOOLS_SAVE_ACTION );
}

/**
 * The sanitizer for one option group.
 *
 * Only the Clean group differs: its values are cron frequency slugs, and
 * anything unrecognised has to become 'off' rather than fall through to plain
 * text, so a tampered form cannot schedule a job.
 *
 * @param string $option
 * @param array  $input
 * @return array
 */
function horsetools_sanitize_for_option( $option, $input ) {
	if ( 'horsetools_clean_settings' === $option && function_exists( 'horsetools_sanitize_clean' ) ) {
		return horsetools_sanitize_clean( $input );
	}
	return horsetools_sanitize_settings_array( $input );
}

/**
 * Write every plugin option present in the submission.
 *
 * Only option names the plugin owns are considered, so the form cannot be
 * edited into writing an unrelated WordPress option.
 */
function horsetools_save_handler() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change these settings.', 'horse-tools' ), 403 );
	}
	check_admin_referer( HORSETOOLS_SAVE_ACTION );

	$written = array();
	foreach ( horsetools_option_names() as $option ) {
		if ( ! isset( $_POST[ $option ] ) || ! is_array( $_POST[ $option ] ) ) {
			continue;
		}
		// $_POST is slashed; the Settings API unslashes for you and this does
		// not, so a quote in a custom CSS box would gain a backslash on every
		// save without this.
		$input = wp_unslash( $_POST[ $option ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised below
		$scope = horsetools_scope_take( $input );
		$value = horsetools_scope_merge(
			horsetools_sanitize_for_option( $option, $input ),
			$option,
			$scope
		);
		update_option( $option, $value );
		$written[] = $option;
	}

	$back = wp_get_referer();
	if ( ! $back ) {
		$back = admin_url( 'admin.php?page=horsetools-options' );
	}
	// 'settings-updated' is what the screens already look for to show the
	// "saved" panel, so the confirmation keeps working unchanged.
	wp_safe_redirect( add_query_arg( 'settings-updated', 'true', remove_query_arg( 'settings-updated', $back ) ) );
	exit;
}
add_action( 'admin_post_' . HORSETOOLS_SAVE_ACTION, 'horsetools_save_handler' );

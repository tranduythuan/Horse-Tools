<?php
/**
 * Horse Tools — where this site's signing keys actually live.
 *
 * Several things in this plugin are protected by a signature keyed on
 * wp_salt('auth'): the PHP snippet signature that refuses to run code written
 * straight into the database, and the "trusted device" cookie that lets a
 * browser skip two-factor authentication for thirty days.
 *
 * The whole point of the snippet signature is stated in inc/php-snippet.php:
 * code planted by "an SQL-injection hole in some other plugin" carries no valid
 * signature and is refused. That promise holds only while the key is somewhere
 * an SQL-injection hole cannot reach.
 *
 * It usually is. WordPress reads AUTH_KEY and AUTH_SALT from wp-config.php,
 * which is a file. But when those constants are missing, empty, still the
 * "put your unique phrase here" placeholder, or duplicated across the eight
 * key/salt constants, WordPress quietly generates the value instead and stores
 * it in the options table — the very place the attacker already is. Nothing in
 * WordPress or in this plugin says so out loud.
 *
 * This file says so. It reads and reports; it never writes wp-config.php.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Which of the eight key/salt constants WordPress consults for cookies.
 *
 * @return string[]
 */
function horsetools_salt_constants() {
	return array(
		'AUTH_KEY',
		'AUTH_SALT',
		'SECURE_AUTH_KEY',
		'SECURE_AUTH_SALT',
		'LOGGED_IN_KEY',
		'LOGGED_IN_SALT',
		'NONCE_KEY',
		'NONCE_SALT',
	);
}

/**
 * Where the material behind wp_salt('auth') comes from.
 *
 * Deliberately tested by its *result* rather than by inspecting constants.
 * wp_salt() ignores a constant that is empty, that is still the placeholder, or
 * whose value is shared with another of the eight — reimplementing those rules
 * here would mean maintaining a second copy of them and being wrong whenever
 * WordPress changed. Comparing the value WordPress actually produced against
 * what the database holds cannot drift.
 *
 * wp_salt('auth') returns AUTH_KEY . AUTH_SALT. Each half falls back on its own,
 * so a site can have one half in a file and the other in the database. That is
 * still safe — the half an attacker cannot see is 64 characters of entropy, and
 * the key is the pair — so it is reported, not alarmed about.
 *
 * @return string 'file' | 'partial' | 'db' | 'unknown'
 */
function horsetools_salt_location() {
	if ( ! function_exists( 'wp_salt' ) || ! function_exists( 'get_site_option' ) ) {
		return 'unknown';
	}

	$auth = (string) wp_salt( 'auth' );
	if ( '' === $auth ) {
		return 'unknown';
	}

	$db_key  = (string) get_site_option( 'auth_key', '' );
	$db_salt = (string) get_site_option( 'auth_salt', '' );

	// Both halves came out of the database: everything needed to forge a
	// signature is sitting in a table.
	if ( '' !== $db_key && '' !== $db_salt && hash_equals( $db_key . $db_salt, $auth ) ) {
		return 'db';
	}

	// One half did. The other is still unknown to anyone who only has the
	// database, so signatures cannot be forged — worth tidying, not worth
	// alarming about.
	$key_from_db  = '' !== $db_key && 0 === strpos( $auth, $db_key );
	$salt_from_db = '' !== $db_salt && strlen( $auth ) > strlen( $db_salt )
		&& substr( $auth, -strlen( $db_salt ) ) === $db_salt;
	if ( $key_from_db || $salt_from_db ) {
		return 'partial';
	}

	return 'file';
}

/**
 * The constants that are missing, empty, still the placeholder, or duplicated.
 *
 * Used to tell the owner exactly which lines to paste rather than handing them
 * all eight and letting them work it out.
 *
 * @return string[] Constant names that WordPress cannot use.
 */
function horsetools_salt_unusable() {
	$seen   = array();
	$counts = array();
	foreach ( horsetools_salt_constants() as $name ) {
		if ( ! defined( $name ) ) {
			continue;
		}
		$value = (string) constant( $name );
		$seen[ $name ] = $value;
		if ( '' !== $value ) {
			$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + 1 : 1;
		}
	}

	$bad = array();
	foreach ( horsetools_salt_constants() as $name ) {
		if ( ! isset( $seen[ $name ] ) ) {
			$bad[] = $name; // not defined at all
			continue;
		}
		$value = $seen[ $name ];
		if ( '' === $value
			|| 'put your unique phrase here' === $value
			|| ( isset( $counts[ $value ] ) && $counts[ $value ] > 1 ) ) {
			$bad[] = $name;
		}
	}
	return $bad;
}

/**
 * Eight fresh define() lines, ready to paste.
 *
 * Generated with the same call WordPress uses for its own fallback values, so
 * the result is exactly what wordpress.org's salt service would give. Generated
 * on demand and never stored anywhere — this function's output is a secret that
 * exists for as long as the page is open.
 *
 * @return string
 */
function horsetools_salt_suggest() {
	$out = '';
	foreach ( horsetools_salt_constants() as $name ) {
		$value = wp_generate_password( 64, true, true );
		$out  .= sprintf( "define( '%s', '%s' );\n", $name, addcslashes( $value, "'\\" ) );
	}
	return $out;
}

/**
 * Say so on the plugin's own screens when the keys are in the database.
 *
 * Not a general admin notice: this is advice about Horse Tools' own promises,
 * so it belongs where the owner is already looking at Horse Tools. The fresh
 * keys are printed here rather than on a page of their own — there is nothing
 * to configure, only something to copy.
 */
function horsetools_salt_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! horsetools_is_plugin_screen() ) {
		return;
	}
	if ( 'db' !== horsetools_salt_location() ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p><strong><?php esc_html_e( 'This site’s signing keys are stored in the database, not in wp-config.php.', 'horse-tools' ); ?></strong></p>
		<p><?php esc_html_e( 'Two Horse Tools protections are signed with those keys: the PHP snippet signature, which is meant to refuse code written straight into the database, and the “trusted device” cookie that skips two-factor authentication. Anyone who can read your database can read the keys, so both can be forged. Moving the keys into wp-config.php — a file — restores them.', 'horse-tools' ); ?></p>
		<details>
			<summary style="cursor:pointer;font-weight:600;margin:6px 0"><?php esc_html_e( 'Show new keys to paste', 'horse-tools' ); ?></summary>
			<p class="description" style="margin:8px 0">
				<?php esc_html_e( 'Paste all eight lines into wp-config.php, above the line that requires wp-settings.php. Replace any existing lines with the same names. Everyone signed in — including you — will be signed out afterwards, which is the point: it invalidates every cookie issued with the old keys.', 'horse-tools' ); ?>
			</p>
			<textarea readonly rows="9" class="large-text code" id="ht-salt-lines" onclick="this.select()"><?php echo esc_textarea( horsetools_salt_suggest() ); ?></textarea>
			<p>
				<button type="button" class="button" onclick="var t=document.getElementById('ht-salt-lines');t.select();document.execCommand('copy');this.textContent=<?php echo wp_json_encode( __( 'Copied', 'horse-tools' ) ); ?>;">
					<?php esc_html_e( 'Copy all eight lines', 'horse-tools' ); ?>
				</button>
			</p>
		</details>
	</div>
	<?php
}
add_action( 'admin_notices', 'horsetools_salt_notice' );

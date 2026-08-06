<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
if (isset($horsetools_options['goo-log1'])){
// Hàm chung để khởi tạo và cấu hình \Google\Client
function horsetools_initialize_google_client() {
    $autoload = horsetools_google_autoload_path();
    if ( ! $autoload ) {
        return null;
    }
    require_once( $autoload );
    global $horsetools_options;
    $urlgoogleset = admin_url('admin-ajax.php?action=horsetools_login_google');
    $setClientId = !empty($horsetools_options['goo-log11']) ? $horsetools_options['goo-log11'] : '123456789';
    $setClientSecret = !empty($horsetools_options['goo-log12']) ? $horsetools_options['goo-log12'] : '123456789';
    $gClient = new \Google\Client();
    $gClient->setClientId($setClientId);
    $gClient->setClientSecret($setClientSecret);
    $gClient->setApplicationName("Google login");
    $gClient->setRedirectUri($urlgoogleset);
    // "plus.login" belonged to Google+, which was shut down in 2019 and now
    // causes an invalid_scope error. openid/email/profile are the current
    // scopes that back userinfo_v2_me and the verified-email claim.
    $gClient->setScopes(array('openid', 'email', 'profile'));
    return $gClient;
}
/**
 * Issue a single-use OAuth `state` bound to THIS browser.
 *
 * wp_create_nonce() is wrong for this. For a logged-out visitor the nonce is
 * hashed over user ID 0 and an empty session token, so every anonymous visitor
 * on the site receives the same value for ~24 hours — and the shortcode prints
 * it in public HTML. An attacker could scrape it, start the consent flow with
 * their own Google account, and hand the resulting callback URL to a victim,
 * silently logging that victim into the attacker's account (login CSRF).
 *
 * A random value stored in a transient and mirrored in a cookie ties the
 * callback to the browser that actually started the flow, which is what
 * `state` is for.
 */
function horsetools_issue_oauth_state() {
    $state = wp_generate_password( 32, false );
    set_transient( 'horsetools_oauth_' . $state, 1, 15 * MINUTE_IN_SECONDS );
    setcookie(
        'horsetools_oauth_state',
        $state,
        array(
            'expires'  => time() + ( 15 * MINUTE_IN_SECONDS ),
            'path'     => COOKIEPATH ? COOKIEPATH : '/',
            'domain'   => COOKIE_DOMAIN,
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        )
    );
    return $state;
}

/**
 * Validate and consume the state returned by Google. Single use.
 */
function horsetools_consume_oauth_state( $state ) {
    $state = is_string( $state ) ? sanitize_text_field( $state ) : '';
    if ( '' === $state || ! isset( $_COOKIE['horsetools_oauth_state'] ) ) {
        return false;
    }
    $cookie = sanitize_text_field( wp_unslash( $_COOKIE['horsetools_oauth_state'] ) );
    if ( ! hash_equals( $cookie, $state ) ) {
        return false;
    }
    if ( ! get_transient( 'horsetools_oauth_' . $state ) ) {
        return false;
    }
    delete_transient( 'horsetools_oauth_' . $state );
    return true;
}

// Hàm trả về URL đăng nhập
function horsetools_get_google_login_url() {
    $gClient = horsetools_initialize_google_client();
    if ( ! $gClient ) {
        return '';
    }
    $gClient->setState( horsetools_issue_oauth_state() );
    $login_url = $gClient->createAuthUrl();
    return $login_url;
}
# Tạo shorcode đăng nhập
function horsetools_login_shortcode_google() {
    $login_url = horsetools_get_google_login_url();
    $btnContent = '
	    <style>
			.horsetools-google a {
				font-weight: bold;
				display: block;
				margin: 0 auto;
				background: #f3f3f3;
				padding: 10px;
				border-radius: 10px;
				text-align: center;
				text-decoration: none;
				margin-bottom: 20px;
				color: #333 !important;
			}
			.horsetools-google a:hover{opacity:0.6}
            .horsetools-google a img{
				width:30px !important;
				vertical-align: middle;
				margin-right:15px;
			}
        </style>
	';
    if (!is_user_logged_in()) {
        if ( '' === $login_url ) {
            // The Google library could not be unpacked; don't print a dead button.
            return '';
        }
        return $btnContent . '<div class="horsetools-google"><a title="Google login" href="' . esc_url($login_url) . '"><img alt="Gooogle login" src="'. HORSETOOLS_URL . 'img/google.svg' .'"/> '. __('Sign in with Google', 'horse-tools') .'</a></div>';
    } else {
        return $btnContent . '<div class="horsetools-google"><a href="' . esc_url(wp_logout_url()) . '">'. __('Log out', 'horse-tools') .'</a></div>';
    }
}
add_shortcode('google-login', 'horsetools_login_shortcode_google');

/**
 * Finish a Google sign-in — the only place this flow issues a session.
 *
 * Google has proved the person controls that mailbox. That is one factor. If
 * the account also carries two-factor authentication, this must not hand out a
 * session: it used to call wp_set_auth_cookie() directly, which fires no
 * wp_login hook, so the second factor never ran and an administrator with 2FA
 * switched on could be signed in by whoever held their Google account. The
 * password door was locked and this one was not.
 *
 * So: when a second factor is owed, no cookie is set here at all. The browser
 * is sent to wp-login.php, which asks for the code and issues the session
 * itself once the code is right. When none is owed, the cookie is set and
 * wp_login is fired the way core does after wp_signon — which also gives every
 * other plugin (login logs, WooCommerce sessions, security tools) the hook it
 * has always expected and never received from this path.
 *
 * @param WP_User|false $user
 */
function horsetools_google_finish_login( $user ) {
	if ( ! ( $user instanceof WP_User ) ) {
		wp_safe_redirect( home_url() );
		exit;
	}

	$dest = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'myaccount' ) : admin_url();

	// The 2FA module is loaded only when the setting is on, so ask before using.
	if ( function_exists( 'horsetools_2fa_second_factor_due' )
		&& horsetools_2fa_second_factor_due( $user->ID ) ) {
		wp_safe_redirect( horsetools_2fa_handoff_url( $user->ID, $dest ) );
		exit;
	}

	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID, true );
	do_action( 'wp_login', $user->user_login, $user );
	wp_safe_redirect( $dest );
	exit;
}

# cau hinh nhan thong tin
function horsetools_login_google() {
    global $horsetools_options;
	if ( empty($_GET['state']) || ! horsetools_consume_oauth_state( wp_unslash( $_GET['state'] ) ) ) {
		wp_die( esc_html__( 'Security check failed. Please start the sign-in again.', 'horse-tools' ) );
	}
	$gClient = horsetools_initialize_google_client();
	if ( ! $gClient ) {
		wp_safe_redirect( home_url() );
		exit;
	}
	if ( isset($_GET['code']) && is_string( $_GET['code'] ) ) {
        $token = $gClient->fetchAccessTokenWithAuthCode( sanitize_text_field( wp_unslash( $_GET['code'] ) ) );
        if (!is_array($token) || isset($token["error"])) {
            // Trao đổi token thất bại - dừng ngay
            wp_safe_redirect(home_url());
            exit;
        }
        $oAuth = new \Google\Service\Oauth2($gClient);
        $userData = $oAuth->userinfo_v2_me->get();
        if (empty($userData) || empty($userData->getEmail())) {
            wp_safe_redirect(home_url());
            exit;
        }
        // Bắt buộc email phải được Google xác minh, nếu không kẻ tấn công có thể
        // khai báo email của quản trị viên và chiếm tài khoản
        if (!filter_var($userData->getVerifiedEmail(), FILTER_VALIDATE_BOOLEAN)) {
            wp_die( esc_html__( 'Your Google account email address is not verified.', 'horse-tools' ) );
        }
        $user_email = sanitize_email($userData->getEmail());
        if (empty($user_email) || !is_email($user_email)) {
            wp_safe_redirect(home_url());
            exit;
        }
        if (!email_exists($user_email)) {
            // Honour the site's own registration setting. Without this, a site
            // owner who deliberately turned off "Anyone can register" still
            // gets open registration through this endpoint.
            if ( ! get_option('users_can_register') ) {
                wp_die( esc_html__( 'Registration is disabled on this site.', 'horse-tools' ) );
            }
            $random_password = wp_generate_password();
            $user_login = sanitize_user(strstr($user_email, '@', true));
			// Không cho phép vai trò tuỳ ý từ tuỳ chọn, và luôn cấm administrator
			$roleuser = !empty($horsetools_options['goo-role1']) ? $horsetools_options['goo-role1'] : 'subscriber';
			if ( ! function_exists( 'get_editable_roles' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}
			$allowed_roles = array_keys( get_editable_roles() );
			if ( ! is_string( $roleuser ) || 'administrator' === $roleuser || ! in_array( $roleuser, $allowed_roles, true ) ) {
				$roleuser = 'subscriber';
			}
            $new_user_id = wp_insert_user(array(
                'user_login'        => $user_login,
                'user_pass'         => $random_password,
                'user_email'        => $user_email,
                'first_name'        => $userData->getGivenName(),
                'last_name'         => $userData->getFamilyName(),
                'user_registered'   => current_time('mysql'),
                'role'              => $roleuser,
            ));
            if (!is_wp_error($new_user_id)) {
                //wp_new_user_notification($new_user_id);
                horsetools_google_finish_login( get_user_by( 'id', $new_user_id ) );

                exit;
            }
        } else {
            $user = get_user_by('email', $user_email);
            if ($user) {
                horsetools_google_finish_login( $user );
                exit;
            }
        }
        // Handle error or redirect to homepage
        wp_safe_redirect(home_url());
        exit;
    } else {
        // Handle error or redirect to homepage
        wp_safe_redirect(home_url());
        exit;
    }
}
add_action('wp_ajax_horsetools_login_google', 'horsetools_login_google');
add_action('wp_ajax_nopriv_horsetools_login_google', 'horsetools_login_google');
# hien thị vao form dang nhap mac dinh
if (isset($horsetools_options['goo-log13'])){
function horsetools_login_form_social_login() {
   echo do_shortcode( '[google-login]' );
}
add_action('login_form', 'horsetools_login_form_social_login');
}
}
# Thêm nút đăng nhập Google vào form đăng nhập của WooCommerce
if (isset($horsetools_options['goo-log14'])){
function horsetools_google_login_to_woocommerce_login_form() {
    echo do_shortcode( '[google-login]' );
}
add_action('woocommerce_login_form', 'horsetools_google_login_to_woocommerce_login_form');
}
/**
 * Verify a reCAPTCHA token with Google.
 *
 * Replaces four near-identical blocks that each used file_get_contents() with
 * a stream context. That was wrong in several ways at once:
 *
 *   - on a host with allow_url_fopen=0 (a common hardened default) the call
 *     returns false, the caller returned a WP_Error, and NOBODY — including
 *     the administrator — could log in, with no way back except editing the
 *     database by hand;
 *   - a null secret key is silently dropped by http_build_query(), so ticking
 *     the option before pasting the secret produced the same total lockout;
 *   - there was no timeout, so a hung request to Google stalled every login
 *     for default_socket_timeout (60s);
 *   - it bypassed every WP HTTP filter and any configured proxy.
 *
 * A transport failure now fails OPEN and logs. Locking the owner out of their
 * own site is a worse outcome than letting a login through while Google is
 * unreachable — the password check still has to pass either way.
 *
 * @param string      $token           Token from the client.
 * @param string|null $expected_action v3 action name, or null for v2.
 * @param float|null  $min_score       v3 score threshold, or null for v2.
 * @return true|WP_Error True when the token is good (or unverifiable).
 */
function horsetools_recaptcha_verify( $token, $expected_action = null, $min_score = null ) {
	global $horsetools_options;

	$secret = !empty($horsetools_options['goo-cap12']) ? $horsetools_options['goo-cap12'] : '';
	if ( '' === $secret ) {
		// Misconfigured, not a failed challenge. Ticking the reCAPTCHA option
		// before pasting the secret key used to lock every user, administrator
		// included, out of the login form with no way back except editing
		// wp_options by hand. Treat a missing secret as "feature off".
		return true;
	}
	if ( ! is_string( $token ) || '' === $token ) {
		return new WP_Error( 'recaptcha_error', __( 'Please complete the reCAPTCHA to verify that you are not a robot', 'horse-tools' ) );
	}

	$response = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 5,
			'body'    => array(
				'secret'   => $secret,
				'response' => $token,
				'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'horse-tools: reCAPTCHA transport failure: ' . $response->get_error_message() );
		return true; // fail open — see the note above
	}

	$body = json_decode( wp_remote_retrieve_body( $response ) );
	if ( ! is_object( $body ) ) {
		error_log( 'horse-tools: reCAPTCHA returned an unreadable response.' );
		return true;
	}

	$invalid = new WP_Error( 'recaptcha_error', __( 'The reCAPTCHA is invalid', 'horse-tools' ) );

	if ( empty( $body->success ) ) {
		return $invalid;
	}

	// A site key is public and is shared across every form on the site, so a
	// token minted for another action or another domain must not be accepted.
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( isset( $body->hostname ) && '' !== (string) $body->hostname
		&& 0 !== strcasecmp( (string) $body->hostname, (string) $host ) ) {
		return $invalid;
	}

	if ( null !== $expected_action ) {
		if ( ! isset( $body->action ) || 0 !== strcasecmp( (string) $body->action, $expected_action ) ) {
			return $invalid;
		}
	}

	if ( null !== $min_score ) {
		if ( ! isset( $body->score ) || (float) $body->score < (float) $min_score ) {
			return $invalid;
		}
	}

	return true;
}

/**
 * login_form_middle is a FILTER that builds a string, not an action. Hooking an
 * echoing callback to it emits the markup before the form is printed, so the
 * hidden g-recaptcha-response input lands outside the <form> and is never
 * submitted — which made every wp_login_form() based theme login impossible.
 */
function horsetools_recaptcha_login_form_middle( $content, $callback ) {
	ob_start();
	call_user_func( $callback );
	return $content . ob_get_clean();
}

/**
 * v3 score threshold. Filterable so a site can tune it without editing code.
 */
function horsetools_recaptcha_threshold() {
	global $horsetools_options;
	$threshold = isset($horsetools_options['goo-cap13']) && is_numeric($horsetools_options['goo-cap13'])
		? (float) $horsetools_options['goo-cap13']
		: 0.5;
	$threshold = min( 1.0, max( 0.0, $threshold ) );
	return (float) apply_filters( 'horsetools_recaptcha_threshold', $threshold );
}

# add font captcha v2 vao form login
if (isset($horsetools_options['goo-cap1']) && $horsetools_options['goo-cap1'] == 'V2'){
function horsetools_authentication_login_form(){
	global $horsetools_options;
	$site_key = !empty($horsetools_options['goo-cap11']) ? $horsetools_options['goo-cap11'] : NULL;
    ?>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($site_key); ?>"></div>
	<style>.login form .g-recaptcha{margin-left:-15px;margin-bottom:10px;} form.woocommerce-form.woocommerce-form-login.login .g-recaptcha, form.woocommerce-form.woocommerce-form-register.register .g-recaptcha{margin-bottom:10px;}</style>
    <?php
}
add_action( 'login_form', 'horsetools_authentication_login_form' );
add_filter( 'login_form_middle', function ( $content ) {
	return horsetools_recaptcha_login_form_middle( $content, 'horsetools_authentication_login_form' );
} );
add_action('register_form', 'horsetools_authentication_login_form');
// xu ly woo
add_action('woocommerce_login_form', 'horsetools_authentication_login_form');
add_action('woocommerce_register_form', 'horsetools_authentication_login_form');
// xu ly dang nhap
function horsetools_authentication_login_verify( $user, $username ) {
    $token  = isset($_POST['g-recaptcha-response']) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
    // v2 has no score and no action, so only success + hostname are checked.
    $result = horsetools_recaptcha_verify( $token );
    return is_wp_error( $result ) ? $result : $user;
}
add_filter( 'wp_authenticate_user', 'horsetools_authentication_login_verify', 10, 2 );
// xu ly dang ky
function horsetools_authentication_register_verify($errors) {
    $token  = isset($_POST['g-recaptcha-response']) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
    $result = horsetools_recaptcha_verify( $token );
    if ( is_wp_error( $result ) ) {
        $errors->add( 'recaptcha_error', $result->get_error_message() );
    }
    return $errors;
}
add_filter('registration_errors', 'horsetools_authentication_register_verify');
add_filter('woocommerce_process_registration_errors', 'horsetools_authentication_register_verify', 10, 3);
}
# add font captcha v3 vao form login
if (isset($horsetools_options['goo-cap1']) && $horsetools_options['goo-cap1'] == 'V3'){
function horsetools_authentication_login_form_v3() {
	global $horsetools_options;
	$site_key = !empty($horsetools_options['goo-cap11']) ? $horsetools_options['goo-cap11'] : NULL;
    ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($site_key); ?>"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo esc_js($site_key); ?>', {action: 'login'})
            .then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
            });
        });
    </script>
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
    <?php
}
/**
 * Same widget, but reporting action "register".
 *
 * The action name is what lets the server tell a login token from a
 * registration token; emitting "login" on both forms made the check
 * meaningless, since one token would satisfy either endpoint.
 */
function horsetools_authentication_register_form_v3() {
	global $horsetools_options;
	$site_key = !empty($horsetools_options['goo-cap11']) ? $horsetools_options['goo-cap11'] : NULL;
    ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($site_key); ?>"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo esc_js($site_key); ?>', {action: 'register'})
            .then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
            });
        });
    </script>
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
    <?php
}
add_action('login_form', 'horsetools_authentication_login_form_v3');
add_filter('login_form_middle', function ( $content ) {
	return horsetools_recaptcha_login_form_middle( $content, 'horsetools_authentication_login_form_v3' );
});
add_action('register_form', 'horsetools_authentication_register_form_v3');
// xu ly woo
add_action('woocommerce_login_form', 'horsetools_authentication_login_form_v3');
// WooCommerce registration had no v3 coverage at all: neither the widget nor
// the verifier was registered, so a store with v3 selected had an unprotected
// registration form. Both are wired up now.
add_action('woocommerce_register_form', 'horsetools_authentication_register_form_v3');
// xu ly dang nhap
function horsetools_authentication_login_verify_v3($user, $username) {
    $token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
    $result = horsetools_recaptcha_verify( $token, 'login', horsetools_recaptcha_threshold() );
    return is_wp_error( $result ) ? $result : $user;
}
add_filter('wp_authenticate_user', 'horsetools_authentication_login_verify_v3', 10, 2);
// xu ly dang ky
function horsetools_authentication_register_verify_v3($errors) {
    $token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
    $result = horsetools_recaptcha_verify( $token, 'register', horsetools_recaptcha_threshold() );
    if ( is_wp_error( $result ) ) {
        $errors->add( 'recaptcha_error', $result->get_error_message() );
    }
    return $errors;
}
add_filter('registration_errors', 'horsetools_authentication_register_verify_v3');
add_filter('woocommerce_process_registration_errors', 'horsetools_authentication_register_verify_v3', 10, 3);
}







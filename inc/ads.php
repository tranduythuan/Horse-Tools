<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_ads_options;
# The "ads click" feature was removed in Horse Tools 1.0.0.
#
# It registered a document-wide click listener for every non-administrator
# visitor and, on any click, opened an affiliate URL in a deliberately
# off-screen window (left=2000,top=2000, no toolbar or scrollbars), throttled
# per destination via localStorage. That is affiliate cookie stuffing: it
# fabricates referral credit without the visitor's knowledge or consent.
#
# It is fraud against both the visitor and the affiliate network, it breaks
# Google AdSense policy, and it is the kind of thing that gets a domain
# blacklisted. It is not coming back. The ads-click* options are left in the
# database untouched but are never read; uninstall.php clears them with the
# rest of the ads settings.

# adsense
if (isset($horsetools_ads_options['ads-sense1'])) {
function horsetools_adsense_code(){
    global $horsetools_ads_options;
    if (!empty($horsetools_ads_options['ads-sense11'])) {
		// Intentionally raw ad code: allow-listed in inc/sanitize.php, writable only with manage_options.
		echo $horsetools_ads_options['ads-sense11'];
	}
}
add_action('wp_head', 'horsetools_adsense_code');
// vi tri tuy chinh
function horsetools_adsense_after( $insertion, $paragraph_interval, $content, $tag = 'p' ) {
    $pattern = "/(<$tag\b[^>]*>)(.*?)(<\/$tag>)/is";
    $matches = array();
    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    $new_content = '';
    $last_pos = 0;
    foreach ($matches[0] as $index => $match) {
        $pos = $match[1];
        $length = strlen($match[0]);
        // Chèn nội dung sau mỗi nhóm $paragraph_interval thẻ
        if ( ($index + 1) % $paragraph_interval == 0 ) {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $match[0] . $insertion;
        } else {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $match[0];
        }
        
        $last_pos = $pos + $length;
    }
    $new_content .= substr($content, $last_pos);
    return $new_content;
}
// add vao content
function horsetools_adsense_content_custom($content) {
    global $horsetools_ads_options;
	if (isset($horsetools_ads_options['posttype']) && !empty($horsetools_ads_options['posttype'])) {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $horsetools_ads_options['posttype'])) {
			if (!empty($horsetools_ads_options['ads-sense-p1'])) {
				// Intentionally raw ad code: allow-listed in inc/sanitize.php, writable only with manage_options.
				$content = $horsetools_ads_options['ads-sense-p1'] . $content;
			}
			if (!empty($horsetools_ads_options['ads-sense-c3'])) {
				$tag = !empty($horsetools_ads_options['ads-sense-c1']) ? sanitize_text_field($horsetools_ads_options['ads-sense-c1']) : 'p';
				$paragraph_interval = !empty($horsetools_ads_options['ads-sense-c2']) ? (int)sanitize_text_field($horsetools_ads_options['ads-sense-c2']) : 10;
				// Intentionally raw ad code: allow-listed in inc/sanitize.php, writable only with manage_options.
				$insertion = $horsetools_ads_options['ads-sense-c3']; 
				$content = horsetools_adsense_after($insertion, $paragraph_interval, $content, $tag);
			}
			if (!empty($horsetools_ads_options['ads-sense-p2'])) {
				// Intentionally raw ad code: allow-listed in inc/sanitize.php, writable only with manage_options.
				$content .= $horsetools_ads_options['ads-sense-p2'];
			}
		}
	}
    return $content;
}
add_filter('the_content', 'horsetools_adsense_content_custom');
}
# ads.txt
class horsetools_ads_txt {
    public function setup(): void {
        add_action( 'init', [ $this, 'add_rewrite_rules' ], 10 );
        add_action( 'init', [ $this, 'flush_rewrite_rules' ], 20 );
        add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
        add_action( 'template_redirect', [ $this, 'output' ], 0 );
    }
    public function add_rewrite_rules(): void {
        // Đăng ký các rule mới
        add_rewrite_rule( '^ads\.txt$', 'index.php?ads_txt=1', 'top' );
    }
    /**
     * Flush once per plugin version, not on every request.
     *
     * This used to call flush_rewrite_rules() unconditionally on `init`, so
     * every single front-end hit regenerated the entire rewrite ruleset and
     * every admin hit additionally rewrote .htaccess from disk. Any visitor
     * could amplify that just by loading pages, and two concurrent admin
     * requests could interleave their .htaccess writes.
     */
    public function flush_rewrite_rules(): void {
        global $horsetools_ads_options;
        if ( ! isset( $horsetools_ads_options['ads-adstxt1'] ) ) {
            return;
        }
        if ( get_option( 'horsetools_adstxt_flushed' ) === HORSETOOLS_VERSION ) {
            return;
        }
        flush_rewrite_rules();
        update_option( 'horsetools_adstxt_flushed', HORSETOOLS_VERSION );
    }
    public function add_query_vars( array $vars ): array {
        $vars[] = 'ads_txt';
        return $vars;
    }
    public function output(): void {
		global $horsetools_ads_options;
        $is_ads_txt = get_query_var( 'ads_txt' );
		$content = !empty($horsetools_ads_options['ads-adstxt2']) ? $horsetools_ads_options['ads-adstxt2'] : '';
        if (! $is_ads_txt) {
            return;
        }
        // `ads_txt` is a public query var, so /literally/any/path?ads_txt=1
        // would also land here and return HTTP 200 with this body — turning
        // every URL on the domain, 404s included, into a 200. Serve it only
        // for the path the rewrite rule actually targets.
        $path = (string) wp_parse_url( rawurldecode( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH );
        if ( '/ads.txt' !== '/' . ltrim( untrailingslashit( $path ), '/' ) ) {
            return;
        }
        status_header( 200 );
        header( 'Content-Type: text/plain; charset=utf-8', true );
        // Intentionally raw: ads.txt body served as text/plain (allow-listed in inc/sanitize.php).
        echo $content; 
        exit;
    }
}
if (isset($horsetools_ads_options['ads-adstxt1']) ) {
	$horsetools_ads_txt = new horsetools_ads_txt();
	$horsetools_ads_txt->setup();
}


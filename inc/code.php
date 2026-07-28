<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
# Custom CSS / JS blocks.
# NOTE: every value echoed in this file is on the intentionally-raw allow-list in
# inc/sanitize.php (code1, code11, code12, code2..code5). They hold admin-authored
# CSS and <script> markup; escaping them would break the feature. Writable only
# with 'manage_options'.
# css
function horsetools_show_css() {
    global $horsetools_code_options;
    if (!empty($horsetools_code_options['code1'])){
        echo '<style>' . $horsetools_code_options['code1'] . '</style>';
    }
	if (!empty($horsetools_code_options['code11'])){
        echo '<style>@media (max-width: 849px){' . $horsetools_code_options['code11'] . '}</style>';
    }
	if (!empty($horsetools_code_options['code12'])){
        echo '<style>@media (max-width: 549px){' . $horsetools_code_options['code12'] . '}</style>';
    }
}
add_action('wp_head', 'horsetools_show_css');
# head
function horsetools_header_script() {
    global $horsetools_code_options;
    if (!empty($horsetools_code_options['code2'])){
        echo $horsetools_code_options['code2'];
    }
}
add_action('wp_head', 'horsetools_header_script');
# body
function horsetools_body_script() {
    global $horsetools_code_options;
    if (!empty($horsetools_code_options['code3'])) {
        echo $horsetools_code_options['code3'];
    }
}
add_action('wp_body_open', 'horsetools_body_script');
# footer
function horsetools_footer_script() {
    global $horsetools_code_options;
    if (!empty($horsetools_code_options['code4'])){
        echo $horsetools_code_options['code4'];
    }
}
add_action('wp_footer', 'horsetools_footer_script');
# login
function horsetools_login_script() {
	global $horsetools_code_options;
    if (!empty($horsetools_code_options['code5'])){
        // Intentionally raw custom code: allow-listed in inc/sanitize.php, writable only with manage_options.
        echo $horsetools_code_options['code5'];
    }
}
// The add_action() below used to sit inside the function body — the closing
// brace was one line too low — so it never ran and the custom login code
// feature silently did nothing.
add_action('login_head', 'horsetools_login_script', 1);




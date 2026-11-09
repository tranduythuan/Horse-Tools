<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
// style horsetools
global $horsetools_options;
$skin = isset( $horsetools_options['horsetools5'] ) ? $horsetools_options['horsetools5'] : 'Default';

if ( 'WordPress' === $skin ) {
	echo '<style>:root{--color:#2c3338 !important;--colorbar:#2c3338 !important;--gradient:linear-gradient(19deg, rgb(34 113 177) 0%, rgb(48 138 210) 100%) !important;--logo:#2271b1 !important;--icon:#9ca2a7 !important;--nutbor:5px solid #1766a6 !important;--nutbor2:4px solid #111111 !important;--mailbg:#2271b1 !important;--note:#2271b129 !important;}</style>';
} elseif ( 'Dark' === $skin || 'Black' === $skin ) {
	echo '<style>:root{--color:#e0a500 !important;--colorbar:#1e1e1e !important;--gradient:linear-gradient(19deg, rgb(30 30 30) 0%, rgb(45 45 45) 100%) !important;--logo:#ffd766 !important;--icon:#ffd766 !important;--nutbor:5px solid #111111 !important;--nutbor2:4px solid #111111 !important;--mailbg:#222222 !important;--note:#3333333b !important;}</style>';
}
// Default and deprecated skins (Bright, Girly, Coffe, Rocket, Blue) cleanly inherit the base gold & charcoal brand palette in link/htadmin.css.

<?php
/**
 * The font manager, as a tab.
 *
 * Called rather than extracted: the screen reads a font deletion and builds the
 * installed-font list before it prints anything, and that work lives above the
 * markup. Lifting the markup alone leaves it behind and the tab renders empty —
 * the fault that shipped in 1.2.77. Passing true drops the screen's own page
 * chrome so it fits inside the tab.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( function_exists( 'horsetools_font_options_page' ) ) {
	horsetools_font_options_page( true );
}

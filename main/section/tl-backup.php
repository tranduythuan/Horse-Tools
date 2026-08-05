<?php
/**
 * The settings backup screen, as a tab.
 *
 * Called rather than extracted: the screen builds the export payload and prepares any import preview
 * before it prints anything, and that work lives above the
 * markup. Lifting the markup alone leaves it behind and the tab renders empty —
 * the fault that shipped in 1.2.77. Passing true drops the screen's own page
 * chrome so it fits inside the tab.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( function_exists( 'horsetools_export_options_page' ) ) {
	horsetools_export_options_page( true );
}

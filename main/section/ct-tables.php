<?php
/**
 * The stored-tables manager, as a tab.
 *
 * Unlike the other sections this is not extracted markup: the manager is a list
 * screen with its own add/edit/delete actions and its own forms, so it stays
 * one function and is called here. Rendering it inside the group form would
 * nest forms, which browsers silently drop.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( function_exists( 'horsetools_tables_page' ) ) {
	horsetools_tables_page();
}

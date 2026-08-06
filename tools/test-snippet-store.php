<?php
/**
 * Exercise inc/snippet-store.php outside WordPress.
 *
 * The migration gets one attempt on somebody's real snippets, and there is no
 * local WordPress here to try it on, so this stands up just enough of the API
 * the store touches — options, posts, post meta, terms, a small WP_Query — and
 * runs the real file against it. It checks the shape a snippet comes back in,
 * that the migration keeps the old option, that the PHP index only lists what
 * it should, and that tags survive the round trip as a taxonomy.
 *
 * Usage:  php tools/test-snippet-store.php
 */

define( 'ABSPATH', __DIR__ );
define( 'HORSETOOLS_DIR', dirname( __DIR__ ) . '/' );
define( 'OBJECT', 'OBJECT' );

/* ---------------------------------------------------------------- the fake */

$GLOBALS['opt']   = array();
$GLOBALS['posts'] = array();   // id => WP_Post
$GLOBALS['meta']  = array();   // id => key => value
$GLOBALS['terms'] = array();   // id => array of names
$GLOBALS['next']  = 1;
$GLOBALS['acts']  = array();
$GLOBALS['q']     = 0;         // how many list queries were run

class WP_Post {
	public $ID = 0;
	public $post_title = '';
	public $post_name = '';
	public $post_excerpt = '';
	public $post_content = '';
	public $post_type = '';
	public $post_status = '';
}
class WP_Error {
	public $msg;
	public function __construct( $m = '' ) { $this->msg = $m; }
}
function is_wp_error( $t ) { return $t instanceof WP_Error; }

function add_action( $hook, $fn, $pri = 10, $args = 1 ) { $GLOBALS['acts'][ $hook ][] = $fn; }
function add_filter( $hook, $fn, $pri = 10, $args = 1 ) {}
function do_action( $hook ) {
	foreach ( $GLOBALS['acts'][ $hook ] ?? array() as $fn ) { call_user_func( $fn ); }
}
function get_option( $k, $d = false ) { return array_key_exists( $k, $GLOBALS['opt'] ) ? $GLOBALS['opt'][ $k ] : $d; }
function update_option( $k, $v, $a = null ) { $GLOBALS['opt'][ $k ] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['opt'][ $k ] ); return true; }

function sanitize_key( $k ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) ); }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_title( $s ) { return sanitize_key( str_replace( ' ', '-', (string) $s ) ); }
function wp_slash( $v ) { return $v; }
function wp_unslash( $v ) { return $v; }
function wp_parse_args( $a, $d ) { return array_merge( $d, (array) $a ); }
function wp_list_pluck( $rows, $field ) {
	$out = array();
	foreach ( $rows as $r ) { $out[] = is_object( $r ) ? $r->$field : $r[ $field ]; }
	return $out;
}
function register_post_type( $t, $a = array() ) {}
function register_taxonomy( $t, $o, $a = array() ) {}
function __( $s, $d = '' ) { return $s; }
function current_user_can( $c ) { return true; }
function check_ajax_referer( $a, $b ) { return true; }
function wp_send_json_success( $d = null ) {}
function wp_send_json_error( $d = null ) {}

function get_page_by_path( $slug, $out = OBJECT, $type = 'post' ) {
	foreach ( $GLOBALS['posts'] as $p ) {
		if ( $p->post_name === $slug && $p->post_type === $type ) { return $p; }
	}
	return null;
}
function get_post( $id ) { return $GLOBALS['posts'][ $id ] ?? null; }

function wp_insert_post( $args, $err = false ) {
	if ( ! empty( $args['ID'] ) ) {
		$p = $GLOBALS['posts'][ $args['ID'] ];
	} else {
		$p     = new WP_Post();
		$p->ID = $GLOBALS['next']++;
		$GLOBALS['posts'][ $p->ID ] = $p;
	}
	foreach ( array( 'post_title', 'post_name', 'post_excerpt', 'post_content', 'post_type', 'post_status' ) as $f ) {
		if ( isset( $args[ $f ] ) ) { $p->$f = $args[ $f ]; }
	}
	return $p->ID;
}
function wp_delete_post( $id, $force = false ) {
	unset( $GLOBALS['posts'][ $id ], $GLOBALS['meta'][ $id ], $GLOBALS['terms'][ $id ] );
	return true;
}
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['meta'][ $id ][ $key ] ?? ''; }
function update_post_meta( $id, $key, $val ) { $GLOBALS['meta'][ $id ][ $key ] = $val; return true; }

function wp_set_object_terms( $id, $names, $tax, $append = false ) {
	$GLOBALS['terms'][ $id ] = array_values( (array) $names );
	return true;
}
function get_the_terms( $id, $tax ) {
	$names = $GLOBALS['terms'][ $id ] ?? array();
	if ( ! $names ) { return false; }
	$out = array();
	foreach ( $names as $n ) { $out[] = (object) array( 'name' => $n, 'slug' => sanitize_title( $n ) ); }
	return $out;
}
function get_terms( $args ) {
	$count = array();
	foreach ( $GLOBALS['terms'] as $names ) {
		foreach ( $names as $n ) { $count[ $n ] = ( $count[ $n ] ?? 0 ) + 1; }
	}
	ksort( $count );
	$out = array();
	foreach ( $count as $n => $c ) {
		$out[] = (object) array( 'name' => $n, 'slug' => sanitize_title( $n ), 'count' => $c );
	}
	return $out;
}

/** Enough of WP_Query for the store: type, status, s, meta_query, tax_query, order, paging. */
class WP_Query {
	public $posts = array();
	public $found_posts = 0;
	public $max_num_pages = 0;
	public function __construct( $q ) {
		$GLOBALS['q']++;
		$rows = array();
		foreach ( $GLOBALS['posts'] as $p ) {
			if ( $p->post_type !== $q['post_type'] || $p->post_status !== $q['post_status'] ) { continue; }
			if ( ! empty( $q['s'] ) ) {
				// Mirrors horsetools_snip_search_sql(): name, description, slug — never the body.
				$hay = $p->post_title . ' ' . $p->post_excerpt . ' ' . $p->post_name;
				if ( false === stripos( $hay, $q['s'] ) ) { continue; }
			}
			foreach ( $q['meta_query'] ?? array() as $k => $m ) {
				if ( 'relation' === $k ) { continue; }
				if ( (string) get_post_meta( $p->ID, $m['key'], true ) !== (string) $m['value'] ) { continue 2; }
			}
			foreach ( $q['tax_query'] ?? array() as $t ) {
				$slugs = array_map( 'sanitize_title', $GLOBALS['terms'][ $p->ID ] ?? array() );
				if ( ! in_array( $t['terms'], $slugs, true ) ) { continue 2; }
			}
			$rows[] = $p;
		}
		usort( $rows, function ( $a, $b ) { return strcasecmp( $a->post_title, $b->post_title ); } );
		$this->found_posts   = count( $rows );
		$per                 = max( 1, (int) $q['posts_per_page'] );
		$this->max_num_pages = (int) ceil( $this->found_posts / $per );
		$this->posts         = array_slice( $rows, ( max( 1, (int) $q['paged'] ) - 1 ) * $per, $per );
	}
}
function get_posts( $args ) {
	$out = array();
	foreach ( $GLOBALS['posts'] as $p ) {
		if ( $p->post_type !== $args['post_type'] || $p->post_status !== $args['post_status'] ) { continue; }
		foreach ( $args['meta_query'] ?? array() as $k => $m ) {
			if ( 'relation' === $k ) { continue; }
			if ( (string) get_post_meta( $p->ID, $m['key'], true ) !== (string) $m['value'] ) { continue 2; }
		}
		$out[] = ( 'ids' === ( $args['fields'] ?? '' ) ) ? $p->ID : $p;
	}
	return $out;
}

require_once dirname( __DIR__ ) . '/inc/snippet-store.php';

/* --------------------------------------------------------------- the tests */

$pass = 0;
$fail = 0;
function ok( $cond, $what ) {
	global $pass, $fail;
	if ( $cond ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what\n"; }
}
function eq( $got, $want, $what ) {
	ok( $got === $want, $what . ( $got === $want ? '' : ' — got ' . var_export( $got, true ) . ', want ' . var_export( $want, true ) ) );
}

echo "\n1. Migration from the old single option\n";
$GLOBALS['opt']['horsetools_snippets'] = array(
	'hello'  => array( 'title' => 'Hello', 'desc' => 'a greeting', 'content' => '<b>hi</b>', 'on' => 1, 'tags' => array( 'promo', 'contact' ) ),
	'runner' => array( 'title' => 'Runner', 'content' => 'echo 1;', 'on' => 1, 'php' => 1, 'hook' => 'wp_footer', 'scope' => 'front', 'sig' => 'x' ),
	'shelf'  => array( 'title' => 'Shelf', 'content' => 'idle', 'on' => 1, 'php' => 1 ), // php, but no hook
	'off'    => array( 'title' => 'Off', 'content' => 'x', 'on' => 0, 'php' => 1, 'hook' => 'init' ),
	'junk'   => 'not an array',
);
horsetools_snip_migrate();

eq( count( $GLOBALS['posts'] ), 4, 'four records created (the non-array is skipped)' );
ok( ! isset( $GLOBALS['opt']['horsetools_snippets'] ), 'the old option is gone' );
ok( isset( $GLOBALS['opt']['horsetools_snippets_legacy'] ), 'the old option is kept under _legacy' );
eq( count( $GLOBALS['opt']['horsetools_snippets_legacy'] ), 5, 'the kept copy is untouched, junk included' );
eq( get_option( 'horsetools_snip_migrated' ), 1, 'migration marked done' );

echo "\n2. Migration does not run twice\n";
$before = count( $GLOBALS['posts'] );
$GLOBALS['opt']['horsetools_snippets'] = array( 'late' => array( 'title' => 'Late', 'content' => 'x', 'on' => 1 ) );
horsetools_snip_migrate();
eq( count( $GLOBALS['posts'] ), $before, 'a second run creates nothing' );
ok( isset( $GLOBALS['opt']['horsetools_snippets'] ), 'and leaves an option it did not migrate alone' );
unset( $GLOBALS['opt']['horsetools_snippets'] );

echo "\n3. A snippet reads back in the shape the plugin expects\n";
$s = horsetools_snip_read( 'hello' );
eq( $s['title'], 'Hello', 'title' );
eq( $s['desc'], 'a greeting', 'desc' );
eq( $s['content'], '<b>hi</b>', 'content, raw' );
eq( $s['on'], 1, 'on' );
eq( $s['tags'], array( 'promo', 'contact' ), 'tags survive as a plain array' );
eq( horsetools_snip_read( 'nope' ), null, 'a missing slug reads as null' );

echo "\n4. The front-end index holds only what has to run\n";
$idx = horsetools_snip_index();
eq( count( $idx ), 1, 'one entry: enabled + php + a hook' );
eq( $idx[0]['slug'], 'runner', 'and it is the right one' );
eq( $idx[0]['hook'], 'wp_footer', 'with its hook' );
ok( ! in_array( 'shelf', array_column( $idx, 'slug' ), true ), 'a hookless PHP snippet is not in it' );
ok( ! in_array( 'off', array_column( $idx, 'slug' ), true ), 'a disabled PHP snippet is not in it' );

echo "\n5. Flipping one flag touches one record, and the index follows\n";
horsetools_snip_set( 'runner', 'on', 0 );
eq( count( horsetools_snip_index() ), 0, 'switching the runner off empties the index' );
horsetools_snip_set( 'runner', 'on', 1 );
eq( count( horsetools_snip_index() ), 1, 'and switching it back on refills it' );
ok( ! horsetools_snip_set( 'runner', 'nonsense', 1 ), 'an unknown field is refused' );
ok( ! horsetools_snip_set( 'ghost', 'on', 1 ), 'an unknown snippet is refused' );

echo "\n6. The list pages, searches and filters — and never carries a body\n";
for ( $i = 1; $i <= 60; $i++ ) {
	horsetools_snip_write( 'bulk-' . $i, array( 'title' => sprintf( 'Bulk %02d', $i ), 'content' => str_repeat( 'x', 5000 ), 'on' => 1 ) );
}
$page = horsetools_snip_list( array( 'per_page' => 25, 'page' => 1 ) );
eq( count( $page['items'] ), 25, 'a page holds its page size' );
eq( $page['total'], 64, 'the total counts them all' );
eq( $page['pages'], 3, 'three pages' );
ok( ! array_key_exists( 'content', $page['items'][0] ), 'no body in a list row' );
eq( count( horsetools_snip_list( array( 'per_page' => 25, 'page' => 3 ) )['items'] ), 14, 'the last page holds the remainder' );
eq( horsetools_snip_list( array( 'search' => 'Bulk 0' ) )['total'], 9, 'search narrows it' );
eq( horsetools_snip_list( array( 'search' => 'bulk-7' ) )['total'], 1, 'search finds a snippet by the slug people type' );
eq( horsetools_snip_list( array( 'search' => 'xxxxx' ) )['total'], 0, 'search does not match snippet bodies' );
eq( horsetools_snip_list( array( 'tag' => 'promo' ) )['total'], 1, 'tag filter narrows it' );
eq( horsetools_snip_list( array( 'enabled' => true, 'php_only' => true ) )['total'], 2, 'enabled + php_only combine' );

echo "\n7. Tags round-trip, and the tag list is built from them\n";
horsetools_snip_write( 'hello', array_merge( horsetools_snip_read( 'hello' ), array( 'tags' => array( 'promo', 'new' ) ) ) );
eq( horsetools_snip_read( 'hello' )['tags'], array( 'promo', 'new' ), 'tags replaced, not appended' );
$tags = array_column( horsetools_snip_tags(), 'name' );
eq( $tags, array( 'new', 'promo' ), 'the tag list is what is actually in use' );

echo "\n8. Delete\n";
ok( horsetools_snip_remove( 'bulk-1' ), 'removing an existing snippet reports success' );
eq( horsetools_snip_read( 'bulk-1' ), null, 'and it is gone' );
ok( ! horsetools_snip_remove( 'bulk-1' ), 'removing it again reports failure' );
eq( horsetools_snip_list( array( 'per_page' => 100 ) )['total'], 63, 'the count drops by one' );

echo "\n9. A migration that was interrupted finishes on the next attempt\n";
// Wipe the world and set up a half-done migration: the option is still there,
// one of its snippets already has a record, and the "done" flag was never set.
$GLOBALS['posts'] = array();
$GLOBALS['meta']  = array();
$GLOBALS['terms'] = array();
unset( $GLOBALS['opt']['horsetools_snip_migrated'], $GLOBALS['opt']['horsetools_snippets_legacy'] );
$GLOBALS['opt']['horsetools_snippets'] = array(
	'one' => array( 'title' => 'One', 'content' => '1', 'on' => 1 ),
	'two' => array( 'title' => 'Two', 'content' => '2', 'on' => 1 ),
);
horsetools_snip_write( 'one', array( 'title' => 'One', 'content' => '1', 'on' => 1 ) ); // the half that got through
horsetools_snip_migrate();
eq( count( $GLOBALS['posts'] ), 2, 'the missing half is created' );
ok( ! isset( $GLOBALS['opt']['horsetools_snippets'] ), 'and the migration completes rather than retrying forever' );
eq( get_option( 'horsetools_snip_migrated' ), 1, 'marked done' );

echo "\n10. Reading one snippet costs one lookup, not the whole store\n";
$GLOBALS['q'] = 0;
horsetools_snip_read( 'hello' );
horsetools_snip_read( 'bulk-9' );
eq( $GLOBALS['q'], 0, 'reading by slug runs no list query at all' );

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );

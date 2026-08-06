<?php
/**
 * Horse Tools — where snippets live.
 *
 * They used to live in one option, `horsetools_snippets`, holding every snippet
 * and the whole body of each. That is fine for three and wrong for three
 * hundred, and it was wrong in a way nobody could see: reading a single snippet
 * meant unserialising all of them, and `plugins_loaded` did exactly that on
 * every front-end request just to find which ones had asked for a PHP hook. The
 * option was marked not to autoload, which achieved nothing, because the very
 * next hook loaded it anyway.
 *
 * Each snippet is now its own record. Reading one costs one record. Listing
 * them is a query with a page size, so the admin screen and the editor menu
 * stop growing without limit. The one thing the front end still needs on every
 * request — which snippets hook into PHP — is kept in a small index rebuilt on
 * save, so the bodies are never touched unless something actually runs.
 *
 * The old option is not deleted. It is renamed and kept, so a bad migration is
 * recoverable by hand.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_SNIP_CPT   = 'ht_snippet';
const HORSETOOLS_SNIP_TAX   = 'ht_snippet_tag';
const HORSETOOLS_SNIP_INDEX = 'horsetools_snip_index';

/**
 * Fields that are not the title, body, description or tags, and so live in meta.
 * Kept in one place because the migration, the save path and the read path all
 * have to agree on it exactly.
 *
 * Tags are deliberately not here. They were an array in one meta row, which
 * means filtering by tag is a LIKE against a serialised string and listing the
 * tags in use means unserialising every snippet on the site. As a taxonomy both
 * are indexed lookups WordPress already knows how to do, so that is what they
 * are. The array shape callers see is unchanged.
 *
 * @return string[]
 */
function horsetools_snip_meta_keys() {
	return array(
		'on', 'no_admin', 'device', 'login', 'role',
		'date_from', 'date_to',
		'php', 'hook', 'scope', 'sig', 'ok',
	);
}

/**
 * Move the old one-option store into records, once.
 *
 * The original option is renamed rather than deleted. Migrations are the part
 * of a plugin that gets one chance on somebody else's data, and a snippet is
 * something its author typed by hand — losing one is not a bug that can be
 * apologised away. If this ever goes wrong the whole of the old store is still
 * sitting in `horsetools_snippets_legacy`, in the shape it was always in.
 */
function horsetools_snip_migrate() {
	if ( get_option( 'horsetools_snip_migrated' ) ) {
		return;
	}
	$old = get_option( 'horsetools_snippets', null );
	if ( ! is_array( $old ) ) {
		// Nothing to move: a new install, or already dealt with.
		update_option( 'horsetools_snip_migrated', 1, true );
		return;
	}

	// A snippet counts as across if it has a record — whether this run made it
	// or an earlier interrupted run did. Counting only what *this* run wrote
	// would mean a migration that died halfway could never finish: every later
	// attempt would skip the records already there, come up short, and leave
	// the old option in place forever.
	$across = 0;
	foreach ( $old as $slug => $snip ) {
		if ( ! is_array( $snip ) ) {
			continue;
		}
		if ( horsetools_snip_post( $slug ) ) {
			$across++;
			continue; // already there; never overwrite a record with older data.
		}
		if ( horsetools_snip_write( $slug, $snip ) ) {
			$across++;
		}
	}

	// Only once every snippet is safely across.
	if ( $across === count( array_filter( $old, 'is_array' ) ) ) {
		update_option( 'horsetools_snippets_legacy', $old, false );
		delete_option( 'horsetools_snippets' );
		update_option( 'horsetools_snip_migrated', 1, true );
	}
}
add_action( 'admin_init', 'horsetools_snip_migrate' );

/** Register the record type. Hidden: the plugin has its own screen for these. */
function horsetools_snip_register_cpt() {
	register_post_type(
		HORSETOOLS_SNIP_CPT,
		array(
			'public'              => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'hierarchical'        => false,
			'rewrite'             => false,
			'query_var'           => false,
			'can_export'          => true,
			'supports'            => array( 'title', 'editor', 'excerpt' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		)
	);
	register_taxonomy(
		HORSETOOLS_SNIP_TAX,
		HORSETOOLS_SNIP_CPT,
		array(
			'public'            => false,
			'show_ui'           => false,
			'show_in_menu'      => false,
			'show_in_rest'      => false,
			'hierarchical'      => false,
			'rewrite'           => false,
			'query_var'         => false,
			'show_admin_column' => false,
		)
	);
}
add_action( 'init', 'horsetools_snip_register_cpt', 5 );

/**
 * Every tag in use, for the filter menu. One indexed query, whatever the number
 * of snippets.
 *
 * @return array[] Each: slug, name, count.
 */
function horsetools_snip_tags() {
	$terms = get_terms(
		array(
			'taxonomy'   => HORSETOOLS_SNIP_TAX,
			'hide_empty' => true,
			'orderby'    => 'name',
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	$out = array();
	foreach ( $terms as $term ) {
		$out[] = array( 'slug' => $term->slug, 'name' => $term->name, 'count' => (int) $term->count );
	}
	return $out;
}

/**
 * A snippet's tags as the plain array of names the rest of the plugin uses.
 *
 * @param int $post_id
 * @return string[]
 */
function horsetools_snip_tags_of( $post_id ) {
	$terms = get_the_terms( $post_id, HORSETOOLS_SNIP_TAX );
	if ( ! is_array( $terms ) ) {
		return array();
	}
	return wp_list_pluck( $terms, 'name' );
}

/**
 * The post holding a snippet, by its slug.
 *
 * @param string $slug
 * @return WP_Post|null
 */
function horsetools_snip_post( $slug ) {
	$slug = sanitize_key( $slug );
	if ( '' === $slug ) {
		return null;
	}
	// get_page_by_path is served from the object cache after the first call, so
	// a page rendering several snippets does not pay for several queries.
	$post = get_page_by_path( $slug, OBJECT, HORSETOOLS_SNIP_CPT );
	return ( $post instanceof WP_Post ) ? $post : null;
}

/**
 * One snippet as the rest of the plugin expects it: the same array shape the
 * option used to hold, so nothing above this layer had to learn a new one.
 *
 * @param string $slug
 * @return array|null
 */
function horsetools_snip_read( $slug ) {
	$post = horsetools_snip_post( $slug );
	if ( ! $post ) {
		return null;
	}
	$out = array(
		'title'   => $post->post_title,
		'desc'    => $post->post_excerpt,
		'content' => $post->post_content,
		'tags'    => horsetools_snip_tags_of( $post->ID ),
	);
	// The four flags come back as integers, never as the strings post meta
	// stores them in. PHP treats "0" as false, so this looks like a formality
	// here — but this array is also sent to the browser as JSON, and in
	// JavaScript "0" is a non-empty string and therefore true. That turned
	// `on`, `no_admin`, `php` and `ok` inside out on the way to the editor.
	$flags = array( 'on', 'no_admin', 'php', 'ok' );
	foreach ( horsetools_snip_meta_keys() as $key ) {
		$value       = get_post_meta( $post->ID, '_ht_' . $key, true );
		$out[ $key ] = in_array( $key, $flags, true ) ? (int) $value : $value;
	}
	return $out;
}

/**
 * Create or update one snippet.
 *
 * @param string $slug
 * @param array  $data Same shape horsetools_snip_read() returns.
 * @return bool
 */
function horsetools_snip_write( $slug, array $data ) {
	$slug = sanitize_key( $slug );
	if ( '' === $slug ) {
		return false;
	}
	$post = horsetools_snip_post( $slug );
	$args = array(
		'post_type'    => HORSETOOLS_SNIP_CPT,
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => isset( $data['title'] ) ? (string) $data['title'] : $slug,
		'post_excerpt' => isset( $data['desc'] ) ? (string) $data['desc'] : '',
		// Intentionally raw: snippet bodies are HTML/JS/PHP written on a
		// manage_options screen, the same trust model as unfiltered_html.
		'post_content' => isset( $data['content'] ) ? (string) $data['content'] : '',
	);
	if ( $post ) {
		$args['ID'] = $post->ID;
	}
	$id = wp_insert_post( wp_slash( $args ), true );
	if ( is_wp_error( $id ) || ! $id ) {
		return false;
	}
	foreach ( horsetools_snip_meta_keys() as $key ) {
		if ( array_key_exists( $key, $data ) ) {
			update_post_meta( $id, '_ht_' . $key, wp_slash( $data[ $key ] ) );
		}
	}
	if ( array_key_exists( 'tags', $data ) ) {
		$tags = is_array( $data['tags'] ) ? array_filter( array_map( 'trim', $data['tags'] ) ) : array();
		wp_set_object_terms( $id, $tags, HORSETOOLS_SNIP_TAX, false );
	}
	horsetools_snip_reindex();
	return true;
}

/**
 * Change one field of one snippet.
 *
 * The crash guard and the first-run flag each flip a single 0 to a 1. Under the
 * old store that meant reading every snippet on the site, changing one byte and
 * writing them all back — from inside a front-end request, in the crash guard's
 * case from inside a request that had just gone wrong. This touches one row.
 *
 * @param string $slug
 * @param string $key   One of horsetools_snip_meta_keys().
 * @param mixed  $value
 * @return bool
 */
function horsetools_snip_set( $slug, $key, $value ) {
	if ( ! in_array( $key, horsetools_snip_meta_keys(), true ) ) {
		return false;
	}
	$post = horsetools_snip_post( $slug );
	if ( ! $post ) {
		return false;
	}
	update_post_meta( $post->ID, '_ht_' . $key, wp_slash( $value ) );
	// Only these four decide whether a snippet is in the front-end index.
	if ( in_array( $key, array( 'on', 'php', 'hook', 'scope' ), true ) ) {
		horsetools_snip_reindex();
	}
	return true;
}

/**
 * @param string $slug
 * @return bool
 */
function horsetools_snip_remove( $slug ) {
	$post = horsetools_snip_post( $slug );
	if ( ! $post ) {
		return false;
	}
	wp_delete_post( $post->ID, true );
	horsetools_snip_reindex();
	return true;
}

/**
 * Summaries for a screen: everything except the body.
 *
 * The body is what makes a snippet big, and no list needs it — the admin screen
 * used to send every byte of every snippet to the browser to draw a list of
 * names. Callers that genuinely need a body ask for one by slug.
 *
 * @param array $args search, page, per_page, php_only.
 * @return array{items:array, total:int, pages:int}
 */
function horsetools_snip_list( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array( 'search' => '', 'tag' => '', 'page' => 1, 'per_page' => 50, 'php_only' => false, 'enabled' => false )
	);

	$q = array(
		'post_type'              => HORSETOOLS_SNIP_CPT,
		'post_status'            => 'publish',
		'posts_per_page'         => max( 1, (int) $args['per_page'] ),
		'paged'                  => max( 1, (int) $args['page'] ),
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => false,
		'update_post_term_cache' => true, // one query for every row's tags.
	);
	if ( '' !== $args['search'] ) {
		$q['s']              = (string) $args['search'];
		$q['ht_snip_search'] = true;
	}
	if ( '' !== $args['tag'] ) {
		$q['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => HORSETOOLS_SNIP_TAX,
				'field'    => 'slug',
				'terms'    => (string) $args['tag'],
			),
		);
	}
	$meta = array();
	if ( $args['php_only'] ) {
		$meta[] = array( 'key' => '_ht_php', 'value' => '1' );
	}
	if ( $args['enabled'] ) {
		$meta[] = array( 'key' => '_ht_on', 'value' => '1' );
	}
	if ( $meta ) {
		$meta['relation'] = 'AND';
		$q['meta_query']  = $meta; // phpcs:ignore WordPress.DB.SlowDBQuery
	}

	$query = new WP_Query( $q );
	$items = array();
	foreach ( $query->posts as $post ) {
		$meta = array();
		foreach ( horsetools_snip_meta_keys() as $key ) {
			$meta[ $key ] = get_post_meta( $post->ID, '_ht_' . $key, true );
		}
		$items[] = array(
			'slug'      => $post->post_name,
			'title'     => '' !== $post->post_title ? $post->post_title : $post->post_name,
			'desc'      => $post->post_excerpt,
			'on'        => ! empty( $meta['on'] ),
			'no_admin'  => ! empty( $meta['no_admin'] ),
			'device'    => (string) $meta['device'],
			'login'     => (string) $meta['login'],
			'role'      => (string) $meta['role'],
			'date_from' => (string) $meta['date_from'],
			'date_to'   => (string) $meta['date_to'],
			'tags'      => horsetools_snip_tags_of( $post->ID ),
			'php'       => ! empty( $meta['php'] ),
			'php_hook'  => (string) $meta['hook'],
			'php_scope' => '' !== $meta['scope'] ? (string) $meta['scope'] : 'front',
			// A signed snippet whose code no longer matches its signature was
			// changed outside this screen and will refuse to run.
			'php_bad'   => ! empty( $meta['php'] ) && function_exists( 'horsetools_php_signature_ok' )
				&& ! horsetools_php_signature_ok( array( 'content' => $post->post_content, 'sig' => $meta['sig'] ) ),
		);
	}
	return array(
		'items' => $items,
		'total' => (int) $query->found_posts,
		'pages' => (int) $query->max_num_pages,
	);
}

/**
 * Search snippets by what identifies them, not by what is inside them.
 *
 * WordPress's own search looks in the body, which is the wrong place here: a
 * snippet body is HTML, so searching "div" or "class" would match nearly every
 * snippet on the site, while searching the name someone actually types the
 * shortcode with — the slug — would not match at all, because post_name is not
 * one of the columns WordPress searches. This looks in the display name, the
 * description and the slug. Tags have their own filter.
 *
 * @param string   $search Existing WHERE fragment.
 * @param WP_Query $query
 * @return string
 */
function horsetools_snip_search_sql( $search, $query ) {
	if ( ! $query->get( 'ht_snip_search' ) ) {
		return $search;
	}
	global $wpdb;
	$like = '%' . $wpdb->esc_like( (string) $query->get( 's' ) ) . '%';
	return $wpdb->prepare(
		" AND ( {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_name LIKE %s )",
		$like,
		$like,
		$like
	);
}
add_filter( 'posts_search', 'horsetools_snip_search_sql', 10, 2 );

/**
 * Search snippets by name, for the pickers.
 *
 * The editor used to be handed every snippet on the site when the page loaded,
 * and drew them as one long menu. Three is fine. Fifty is a scroll. A thousand
 * is not a menu at all. So the picker asks for what the user typed instead, and
 * the answer is capped at a length a human can actually look at.
 */
function horsetools_snip_search_ajax() {
	check_ajax_referer( 'horsetools_snip_pick', 'nonce' );
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error();
	}
	$search = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';
	$found  = horsetools_snip_list(
		array(
			'search'   => $search,
			'per_page' => 20,
			'enabled'  => true,
		)
	);
	$out = array();
	foreach ( $found['items'] as $snip ) {
		$out[] = array( 'slug' => $snip['slug'], 'title' => $snip['title'] );
	}
	wp_send_json_success(
		array(
			'items' => $out,
			'total' => $found['total'],
			'more'  => $found['total'] > count( $out ),
		)
	);
}
add_action( 'wp_ajax_horsetools_snip_pick', 'horsetools_snip_search_ajax' );

/**
 * The only thing the front end needs on every request.
 *
 * A snippet that runs PHP on a hook has to be attached before that hook fires,
 * so something must be read on every page load. That something is this: slug,
 * hook and scope for the handful of snippets that asked for one. No bodies, no
 * conditions, nothing that grows with the number of snippets that merely sit
 * there waiting to be used in a shortcode.
 *
 * @return array[] Each: slug, hook, scope.
 */
function horsetools_snip_index() {
	$index = get_option( HORSETOOLS_SNIP_INDEX, null );
	if ( ! is_array( $index ) ) {
		$index = horsetools_snip_reindex();
	}
	return $index;
}

/**
 * Rebuild the index from the records. Cheap, and derived — losing it costs
 * nothing but one rebuild.
 *
 * @return array[]
 */
function horsetools_snip_reindex() {
	$posts = get_posts(
		array(
			'post_type'        => HORSETOOLS_SNIP_CPT,
			'post_status'      => 'publish',
			'numberposts'      => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
			// phpcs:ignore WordPress.DB.SlowDBQuery -- runs on save, not on read.
			'meta_query'       => array(
				'relation' => 'AND',
				array( 'key' => '_ht_php', 'value' => '1' ),
				array( 'key' => '_ht_on', 'value' => '1' ),
			),
		)
	);

	$index = array();
	foreach ( $posts as $id ) {
		$hook = (string) get_post_meta( $id, '_ht_hook', true );
		if ( '' === $hook ) {
			continue; // shortcode-only; nothing to attach.
		}
		$post = get_post( $id );
		$index[] = array(
			'slug'  => $post ? $post->post_name : '',
			'hook'  => $hook,
			'scope' => (string) get_post_meta( $id, '_ht_scope', true ),
		);
	}
	update_option( HORSETOOLS_SNIP_INDEX, $index, true );
	return $index;
}

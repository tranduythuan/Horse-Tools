<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Delete posts by type or status, properly.
 *
 * These three tools used to run a bare `DELETE FROM wp_posts`, which is wrong
 * twice over. It leaves the postmeta rows, the term relationships and (for
 * attachments) the files on disk behind — so a tool sold as "clean the
 * database" made it dirtier — and it never fires before_delete_post, so other
 * plugins never learn the post is gone.
 *
 * They also ended in `return true` with no wp_send_json_* call, so
 * admin-ajax.php echoed "0" and died. The JavaScript printed "All revisions
 * have been deleted" regardless — whether 4,000 rows went or none did, and
 * even if the query had failed. The try/catch was dead code: wpdb does not
 * throw by default.
 *
 * @param array $query_args Args merged into the WP_Query lookup.
 * @return int Number of posts actually deleted.
 */
function horsetools_delete_posts_where( array $query_args ) {
    $deleted = 0;
    $defaults = array(
        'posts_per_page'         => 200,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'suppress_filters'       => true,
    );
    do {
        $ids = get_posts( array_merge( $defaults, $query_args ) );
        if ( empty( $ids ) ) {
            break;
        }
        foreach ( $ids as $id ) {
            if ( wp_delete_post( (int) $id, true ) ) {
                $deleted++;
            }
        }
    } while ( count( $ids ) === 200 );

    return $deleted;
}

# nhan nut xoa het ban ghi tam trong csdl làm sach csdl
function horsetools_delete_post_revisions() {
	check_ajax_referer('horsetools_post_revisions', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    $deleted = horsetools_delete_posts_where( array(
        'post_type'   => 'revision',
        'post_status' => 'any',
    ) );
    wp_send_json_success( array( 'deleted_count' => $deleted ) );
}
add_action('wp_ajax_horsetools_delete_revisions', 'horsetools_delete_post_revisions');
# nhan nut xoa ban luu tu dong trong csdl
function horsetools_delete_auto_drafts() {
	check_ajax_referer('horsetools_post_drafts', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    $deleted = horsetools_delete_posts_where( array(
        'post_type'   => 'any',
        'post_status' => 'auto-draft',
    ) );
    wp_send_json_success( array( 'deleted_count' => $deleted ) );
}
add_action('wp_ajax_horsetools_delete_auto_drafts', 'horsetools_delete_auto_drafts');
# xoa tat ca bai trong thung rac
function horsetools_delete_all_trashed_posts() {
	check_ajax_referer('horsetools_post_trashed', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    $deleted = horsetools_delete_posts_where( array(
        'post_type'   => 'any',
        'post_status' => 'trash',
    ) );
    wp_send_json_success( array( 'deleted_count' => $deleted ) );
}
add_action('wp_ajax_horsetools_delete_all_trashed_posts', 'horsetools_delete_all_trashed_posts');
# xoa binh luan cho
/**
 * Permanently delete every comment with the given status.
 *
 * Notes on what changed here and why:
 *  - `sleep(1)` per batch was pure cost. On a site with 5,000 spam comments it
 *    added 25 seconds of doing nothing and pushed the request into a timeout,
 *    at which point the browser reported failure even though thousands of
 *    comments had already been deleted.
 *  - `wp_trash_comment()` immediately followed by `wp_delete_comment($id, true)`
 *    wrote every row twice. The force delete alone is enough.
 *
 * @param string $status Comment status to clear ('hold', 'spam', 'trash').
 * @return int Number deleted.
 */
function horsetools_delete_comments_by_status( $status ) {
    $deleted = 0;
    do {
        $ids = get_comments( array(
            'status' => $status,
            'number' => 200,
            'fields' => 'ids',
        ) );
        if ( empty( $ids ) ) {
            break;
        }
        foreach ( $ids as $id ) {
            if ( wp_delete_comment( (int) $id, true ) ) {
                $deleted++;
            }
        }
        // Every fetched comment is removed, so the next query returns the next
        // batch and the loop terminates. Guard anyway in case a filter blocks
        // a deletion and the same rows come back.
    } while ( count( $ids ) === 200 && $deleted > 0 && $deleted % 200 === 0 );

    return $deleted;
}

function horsetools_del_comments_pend_ajax() {
	check_ajax_referer('horsetools_del_comenpend_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    wp_send_json_success(array('deleted_count' => horsetools_delete_comments_by_status('hold')));
}
add_action('wp_ajax_horsetools_del_comenpend', 'horsetools_del_comments_pend_ajax');
# xoa binh luan spam
function horsetools_del_comments_spam_ajax() {
	check_ajax_referer('horsetools_del_comenspam_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    wp_send_json_success(array('deleted_count' => horsetools_delete_comments_by_status('spam')));
}
add_action('wp_ajax_horsetools_del_comenspam', 'horsetools_del_comments_spam_ajax');
# xoa binh luan thung rac
function horsetools_del_comments_trash_ajax() {
	check_ajax_referer('horsetools_del_comentrash_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    wp_send_json_success(array('deleted_count' => horsetools_delete_comments_by_status('trash')));
}
add_action('wp_ajax_horsetools_del_comentrash', 'horsetools_del_comments_trash_ajax');
# xoa binh luan chua url
function horsetools_del_comments_link_ajax() {
    check_ajax_referer('horsetools_del_comenlink_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    // Two defects fixed here.
    //
    // 1. It never terminated. get_comments() was called with no offset, so a
    //    batch containing no links returned the same 200 rows forever; with the
    //    sleep(1) in the loop the request just span until max_execution_time.
    //    Now the scan pages with an explicit offset and collects IDs first.
    //
    // 2. The match was `link in body OR author filled in the Website field`.
    //    The Website field is an ordinary, optional part of the WordPress
    //    comment form, so the second clause deleted most legitimate comments on
    //    most sites — under a button labelled "delete comments containing
    //    links". Only the comment body is matched now, which is what the label
    //    promises.
    $ids    = array();
    $offset = 0;
    do {
        $batch = get_comments( array(
            'status' => 'all',
            'number' => 200,
            'offset' => $offset,
        ) );
        foreach ( $batch as $comment ) {
            if ( preg_match( '/<a\s|https?:\/\/|www\.[^\s]+/i', $comment->comment_content ) ) {
                $ids[] = (int) $comment->comment_ID;
            }
        }
        $offset += 200;
    } while ( count( $batch ) === 200 );

    $deleted = 0;
    foreach ( $ids as $id ) {
        if ( wp_delete_comment( $id, true ) ) {
            $deleted++;
        }
    }
    wp_send_json_success(array('deleted_count' => $deleted));
}
add_action('wp_ajax_horsetools_del_comenlink', 'horsetools_del_comments_link_ajax');
# xóa ảnh 404 trong media
function horsetools_delete_404_attachments() {
    check_ajax_referer('horsetools_media_del', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    // Was: a single get_posts() capped at 200 with no offset and no
    // post_status, so it scanned at most the newest 200 attachments — and
    // get_posts() defaults post_status to 'publish' while attachments are
    // 'inherit', so it may have scanned nothing at all. The partial result was
    // then reported as a total.
    $deletedCount = 0;
    $scanned      = 0;
    $offset       = 0;
    do {
        $attachments = get_posts( array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => 200,
            'offset'         => $offset,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ) );
        foreach ( $attachments as $attachmentID ) {
            $scanned++;
            $file_path = get_attached_file( $attachmentID );
            if ( $file_path && ! file_exists( $file_path ) ) {
                wp_delete_attachment( $attachmentID, true );
                $deletedCount++;
            } else {
                // Only advance the offset past rows we did not remove;
                // deleting shifts everything after it back by one.
                $offset++;
            }
        }
    } while ( count( $attachments ) === 200 );

    wp_send_json_success(array('deleted_count' => $deletedCount, 'scanned' => $scanned));
}
add_action('wp_ajax_horsetools_delete_media', 'horsetools_delete_404_attachments');
# xoa media thum 404
function horsetools_delete_404_thumbnails() {
    check_ajax_referer('horsetools_media_thum_del', 'security');
    if (!current_user_can('manage_options')) {
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    // This does NOT delete images — it removes entries for thumbnail files that
    // are already missing from disk out of the attachment metadata, so the
    // media library stops referring to files that are not there. The old code
    // reported that count as "images deleted", which was simply untrue. It also
    // scanned only the newest 200 attachments with the wrong post_status.
    $cleanedEntries = 0;
    $scanned        = 0;
    $offset         = 0;
    do {
        $attachments = get_posts( array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => 200,
            'offset'         => $offset,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ) );
        foreach ( $attachments as $attachmentID ) {
            $scanned++;
            $meta = wp_get_attachment_metadata( $attachmentID );
            if ( ! $meta || empty( $meta['sizes'] ) ) {
                continue;
            }
            $base    = get_attached_file( $attachmentID );
            $changed = false;
            foreach ( $meta['sizes'] as $size => $data ) {
                if ( empty( $data['file'] ) || ! $base ) {
                    continue;
                }
                $thumbnail_path = str_replace( basename( $base ), $data['file'], $base );
                if ( ! file_exists( $thumbnail_path ) ) {
                    unset( $meta['sizes'][ $size ] );
                    $cleanedEntries++;
                    $changed = true;
                }
            }
            if ( $changed ) {
                wp_update_attachment_metadata( $attachmentID, $meta );
            }
        }
        $offset += 200;
    } while ( count( $attachments ) === 200 );

    wp_send_json_success(array('deleted_count' => $cleanedEntries, 'scanned' => $scanned));
}
add_action('wp_ajax_horsetools_delete_media_thum', 'horsetools_delete_404_thumbnails');
# ajax xoa file crop
function horsetools_delete_images_by_size_callback() {
    check_ajax_referer('horsetools_delete_crop_nonce', 'security');
    if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    $size_name = isset($_POST['size']) ? sanitize_text_field( wp_unslash( $_POST['size'] ) ) : '';
    if ($size_name !== '') {
        $limit = 500; // Giới hạn số lượng hình muốn xóa
        $deleted_count = horsetools_delete_thumbnails($size_name, $limit);
        wp_send_json_success(__('Deleted', 'horse-tools') .' '. $size_name . ': ' . $deleted_count . ' ' . __('images', 'horse-tools'));
    } else {
        wp_send_json_error(__('No size', 'horse-tools'));
    }
}
add_action('wp_ajax_horsetools_delete_images_by_size', 'horsetools_delete_images_by_size_callback');

/* =========================================================================
 * Count-before-you-delete
 *
 * Every button on the Clean screen used to fire blind: no number beforehand,
 * a hardcoded "done" message afterwards, and — for three of them — a success
 * report even when nothing was deleted. This adds a preview so the button can
 * read "Delete 4,182 revisions (~31 MB)" before it is pressed and report the
 * real figure after.
 * ====================================================================== */

/**
 * The cleanup targets, each with a human label, a count callback and the work
 * to perform. `schedulable` marks the ones safe to run unattended on cron;
 * `comments_links` is deliberately not schedulable because it deletes by a
 * content pattern and must stay a conscious, one-off action.
 *
 * @return array<string,array>
 */
function horsetools_clean_targets() {
	return array(
		'revisions' => array(
			'label'       => __( 'Delete revisions', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_revisions',
			'run'         => 'horsetools_clean_run_revisions',
			'schedulable' => true,
			'group'       => 'content',
		),
		'auto_drafts' => array(
			'label'       => __( 'Delete autosaves', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_auto_drafts',
			'run'         => 'horsetools_clean_run_auto_drafts',
			'schedulable' => true,
			'group'       => 'content',
		),
		'trashed_posts' => array(
			'label'       => __( 'Empty trash', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_trashed_posts',
			'run'         => 'horsetools_clean_run_trashed_posts',
			'schedulable' => true,
			'group'       => 'content',
		),
		'comments_pending' => array(
			'label'       => __( 'Pending comments', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_comments_pending',
			'run'         => 'horsetools_clean_run_comments_pending',
			'schedulable' => false,
			'group'       => 'comment',
		),
		'comments_spam' => array(
			'label'       => __( 'Spam comments', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_comments_spam',
			'run'         => 'horsetools_clean_run_comments_spam',
			'schedulable' => true,
			'group'       => 'comment',
		),
		'comments_trash' => array(
			'label'       => __( 'Trashed comments', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_comments_trash',
			'run'         => 'horsetools_clean_run_comments_trash',
			'schedulable' => true,
			'group'       => 'comment',
		),
		'comments_links' => array(
			'label'       => __( 'Comments containing links', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_comments_links',
			'run'         => null, // manual only; keeps its own AJAX handler
			'schedulable' => false,
			'group'       => 'comment',
		),
		// The two media targets need a per-file disk check, so a precise count
		// is impossible up front. They report the total attachment count as an
		// upper bound (estimated) and are not schedulable.
		'media_404' => array(
			'label'       => __( 'Delete all 404 images', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_media',
			'run'         => null,
			'schedulable' => false,
			'group'       => 'media',
		),
		'media_thumbs_404' => array(
			'label'       => __( 'Delete all 404 thumbnail images', 'horse-tools' ),
			'count'       => 'horsetools_clean_count_media',
			'run'         => null,
			'schedulable' => false,
			'group'       => 'media',
		),
	);
}

/** Count callbacks. Each returns array( count, estimated, size ). */

function horsetools_clean_count_revisions() {
	global $wpdb;
	$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s", 'revision' ) );
	$size  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(LENGTH(post_content)) FROM {$wpdb->posts} WHERE post_type = %s", 'revision' ) );
	return array( 'count' => $count, 'estimated' => false, 'size' => $size );
}

function horsetools_clean_count_auto_drafts() {
	global $wpdb;
	$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = %s", 'auto-draft' ) );
	return array( 'count' => $count, 'estimated' => false, 'size' => 0 );
}

function horsetools_clean_count_trashed_posts() {
	global $wpdb;
	$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = %s", 'trash' ) );
	return array( 'count' => $count, 'estimated' => false, 'size' => 0 );
}

function horsetools_clean_count_comments_pending() {
	$c = wp_count_comments();
	return array( 'count' => (int) $c->moderated, 'estimated' => false, 'size' => 0 );
}

function horsetools_clean_count_comments_spam() {
	$c = wp_count_comments();
	return array( 'count' => (int) $c->spam, 'estimated' => false, 'size' => 0 );
}

function horsetools_clean_count_comments_trash() {
	$c = wp_count_comments();
	return array( 'count' => (int) $c->trash, 'estimated' => false, 'size' => 0 );
}

function horsetools_clean_count_comments_links() {
	// A real count would scan every comment body. Report the total as an upper
	// bound and mark it estimated so the UI does not present it as exact.
	$c = wp_count_comments();
	return array( 'count' => (int) $c->total_comments, 'estimated' => true, 'size' => 0 );
}

function horsetools_clean_count_media() {
	global $wpdb;
	$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s", 'attachment' ) );
	return array( 'count' => $count, 'estimated' => true, 'size' => 0 );
}

/** Run callbacks, shared by the AJAX handlers and the cron driver. */

function horsetools_clean_run_revisions() {
	return horsetools_delete_posts_where( array( 'post_type' => 'revision', 'post_status' => 'any' ) );
}
function horsetools_clean_run_auto_drafts() {
	return horsetools_delete_posts_where( array( 'post_type' => 'any', 'post_status' => 'auto-draft' ) );
}
function horsetools_clean_run_trashed_posts() {
	return horsetools_delete_posts_where( array( 'post_type' => 'any', 'post_status' => 'trash' ) );
}
function horsetools_clean_run_comments_pending() {
	return horsetools_delete_comments_by_status( 'hold' );
}
function horsetools_clean_run_comments_spam() {
	return horsetools_delete_comments_by_status( 'spam' );
}
function horsetools_clean_run_comments_trash() {
	return horsetools_delete_comments_by_status( 'trash' );
}

/**
 * Preview endpoint: every target's current count in one response.
 */
function horsetools_clean_preview_ajax() {
	check_ajax_referer( 'horsetools_clean_preview', 'security' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}
	$out = array();
	foreach ( horsetools_clean_targets() as $id => $target ) {
		$out[ $id ] = call_user_func( $target['count'] );
	}
	wp_send_json_success( $out );
}
add_action( 'wp_ajax_horsetools_clean_preview', 'horsetools_clean_preview_ajax' );

/* =========================================================================
 * Scheduled cleanup
 *
 * One daily cron tick. Each schedulable target carries a frequency
 * (off/daily/weekly/monthly) in horsetools_settings under a clean-cron-<id>
 * key, plus a last-run timestamp in a separate option. On each tick a target
 * runs only if its interval has elapsed, so weekly and monthly work off a
 * daily wake without needing several scheduled events.
 * ====================================================================== */

/**
 * @return array<string,int> frequency slug => interval in seconds (0 = off).
 */
function horsetools_clean_cron_intervals() {
	return array(
		'off'     => 0,
		'daily'   => DAY_IN_SECONDS,
		'weekly'  => WEEK_IN_SECONDS,
		'monthly' => MONTH_IN_SECONDS,
	);
}

/**
 * Keep the daily event scheduled while any target is set to auto-run, and
 * cleared when none is. Called on admin_init, cheap and idempotent.
 */
function horsetools_clean_cron_sync() {
	$intervals = horsetools_clean_cron_intervals();
	$wanted    = false;
	foreach ( horsetools_clean_targets() as $id => $target ) {
		if ( empty( $target['schedulable'] ) ) {
			continue;
		}
		$freq = horsetools_opt( 'clean', 'cron-' . $id, 'off' );
		if ( isset( $intervals[ $freq ] ) && $intervals[ $freq ] > 0 ) {
			$wanted = true;
			break;
		}
	}
	$scheduled = (bool) wp_next_scheduled( 'horsetools_scheduled_clean' );
	if ( $wanted && ! $scheduled ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'horsetools_scheduled_clean' );
	} elseif ( ! $wanted && $scheduled ) {
		wp_clear_scheduled_hook( 'horsetools_scheduled_clean' );
	}
}
add_action( 'admin_init', 'horsetools_clean_cron_sync' );

/**
 * The cron driver. Runs each due target and records when it ran.
 */
function horsetools_scheduled_clean_run() {
	$intervals = horsetools_clean_cron_intervals();
	$last      = get_option( 'horsetools_clean_cron_last', array() );
	if ( ! is_array( $last ) ) {
		$last = array();
	}
	$now = time();

	foreach ( horsetools_clean_targets() as $id => $target ) {
		if ( empty( $target['schedulable'] ) || empty( $target['run'] ) ) {
			continue;
		}
		$freq = horsetools_opt( 'clean', 'cron-' . $id, 'off' );
		if ( empty( $intervals[ $freq ] ) ) {
			continue;
		}
		$due = ! isset( $last[ $id ] ) || ( $now - (int) $last[ $id ] ) >= $intervals[ $freq ];
		if ( ! $due ) {
			continue;
		}
		call_user_func( $target['run'] );
		$last[ $id ] = $now;
	}
	update_option( 'horsetools_clean_cron_last', $last, false );
}
add_action( 'horsetools_scheduled_clean', 'horsetools_scheduled_clean_run' );




<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_clean_options; ?>
			<h2><?php _e( 'CONTENT', 'horse-tools' ); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-trash"></i> <?php _e( 'Optimize deletion of content in the database', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php
				horsetools_clean_button( 'revisions', 'horsetools_delete_revisions', 'horsetools_post_revisions', '#del-result' );
				horsetools_clean_button( 'auto_drafts', 'horsetools_delete_auto_drafts', 'horsetools_post_drafts', '#del-result' );
				horsetools_clean_button( 'trashed_posts', 'horsetools_delete_all_trashed_posts', 'horsetools_post_trashed', '#del-result' );
				?>
				</div>
				<div class="edel" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-result" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Deleting is permanent. Revisions, autosaves and trashed content (posts, pages, products) are removed together with their metadata and attached files.', 'horse-tools' ); ?></p>
			</div>

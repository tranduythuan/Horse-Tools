<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_clean_options; ?>
			<h2><?php _e( 'COMMENT', 'horse-tools' ); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-trash"></i> <?php _e( 'Delete comments', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php
				horsetools_clean_button( 'comments_pending', 'horsetools_del_comenpend', 'horsetools_del_comenpend_nonce', '#del-result2' );
				horsetools_clean_button( 'comments_spam', 'horsetools_del_comenspam', 'horsetools_del_comenspam_nonce', '#del-result2' );
				horsetools_clean_button( 'comments_trash', 'horsetools_del_comentrash', 'horsetools_del_comentrash_nonce', '#del-result2' );
				horsetools_clean_button( 'comments_links', 'horsetools_del_comenlink', 'horsetools_del_comenlink_nonce', '#del-result2', true );
				?>
				</div>
				<div class="edel2" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-result2" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( '"Comments containing links" matches links in the comment body only. The figure shown is the total comment count, not a match count, so confirm carefully.', 'horse-tools' ); ?></p>
			</div>

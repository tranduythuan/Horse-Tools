<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_clean_options; ?>
			<div class="ht-card">
			   <h3><i class="ti ti-photo-off"></i> <?php _e( 'Find and delete all 404 images in media', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php horsetools_clean_button( 'media_404', 'horsetools_delete_media', 'horsetools_media_del', '#del-media', true ); ?>
				</div>
				<div class="emed" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-media" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Removes attachments whose file is missing from disk. Every attachment is scanned, so the count is known only after it runs.', 'horse-tools' ); ?></p>
			   <h3><i class="ti ti-photo-off"></i> <?php _e( 'Find and delete all 404 thumbnail images in media', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php horsetools_clean_button( 'media_thumbs_404', 'horsetools_delete_media_thum', 'horsetools_media_thum_del', '#del-media-thum', true ); ?>
				</div>
				<div class="emed-thum" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-media-thum" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Removes metadata entries for thumbnail files that are missing from disk. It does not delete images.', 'horse-tools' ); ?></p>
			  <h3><i class="ti ti-photo-off"></i> <?php _e( 'Delete cropped image', 'horse-tools' ); ?></h3>
				<div class="ht-card-note ht-del-crop">
				<?php
				global $_wp_additional_image_sizes;
				$image_sizes = get_intermediate_image_sizes();
				if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) > 0 ) {
					$image_sizes = array_merge( $image_sizes, array_keys( $_wp_additional_image_sizes ) );
				}
				$image_sizes = array_unique( $image_sizes );
				foreach ( $image_sizes as $size ) {
					$width = isset( $_wp_additional_image_sizes[ $size ]['width'] ) ? $_wp_additional_image_sizes[ $size ]['width'] : get_option( $size . '_size_w' );
					?>
					<p>
						<a href="javascript:void(0)" class="ht-cropdel" data-size="<?php echo esc_attr( $size ); ?>"><i class="ti ti-trash"></i> <?php echo esc_html( $size . ' (W: ' . $width . ')' ); ?></a>
					</p>
				<?php } ?>
				</div>
				<div id="delete-size-end" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Use with care: your theme may need several image sizes to display correctly.', 'horse-tools' ); ?></p>
			</div>

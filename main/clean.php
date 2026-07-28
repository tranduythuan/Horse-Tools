<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_clean_options_page() {
	ob_start(); 
	?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top">
	</div>
	<div class="ht-wrap2">
	  <div class="ht-box">
		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://tranduythuan.com/" target="_blank">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-thumbtack"></i> <?php _e('CONTENT', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-comment"></i> <?php _e('COMMENT', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="fa-regular fa-image"></i> <?php _e('MEDIA', 'horse-tools'); ?></button>
		</div>
		<div class="ht-main">
			<!-- post -->
			<div class="sotab-box htbox" id="tab1" style="margin-bottom:-60px;">
			<h2><?php _e('CONTENT', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-trash"></i> <?php _e('Optimize deletion of content in the database', 'horse-tools'); ?></h3>
				<div class="ht-del">
				<a href="javascript:void(0)" id="delete-revisions"><i class="fa-regular fa-trash"></i> <?php _e('Delete revisions', 'horse-tools'); ?></a>
				<a href="javascript:void(0)" id="delete-auto-drafts"><i class="fa-regular fa-trash"></i> <?php _e('Delete autosaves', 'horse-tools'); ?></a>
				<a href="javascript:void(0)" id="delete-all-trashed-posts"><i class="fa-regular fa-trash"></i> <?php _e('Empty trash', 'horse-tools'); ?></a>
				</div>
				<div class="edel" style="display:none"><div class="ht-sload"></div> <?php _e('Please wait', 'horse-tools'); ?></div>
				<div id="del-result"></div>
				<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Delete revisions, delete autosaves, delete content (posts, pages, products...) in the trash', 'horse-tools'); ?></p>
			</div>
			</div>
			<!-- comment -->
			<div class="sotab-box htbox" id="tab2" style="display:none;margin-bottom:-60px;">
			<h2><?php _e('COMMENT', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-trash"></i> <?php _e('Delete comments', 'horse-tools'); ?></h3>
				<div class="ht-del">
				<a href="javascript:void(0)" id="delete-comen-pend"><i class="fa-regular fa-trash"></i> <?php _e('Pending', 'horse-tools'); ?></a>
				<a href="javascript:void(0)" id="delete-comen-spam"><i class="fa-regular fa-trash"></i> <?php _e('Spam', 'horse-tools'); ?></a>
				<a href="javascript:void(0)" id="delete-comen-trash"><i class="fa-regular fa-trash"></i> <?php _e('Trash', 'horse-tools'); ?></a>
				<a href="javascript:void(0)" id="delete-comen-link"><i class="fa-regular fa-trash"></i> <?php _e('Content links', 'horse-tools'); ?></a>
				</div>
				<div class="edel2" style="display:none"><div class="ht-sload"></div> <?php _e('Please wait', 'horse-tools'); ?></div>
				<div id="del-result2"></div> 
				<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Note: deleting "Content links" will delete all comments that have a link in the body or url input field', 'horse-tools'); ?></p>
			</div>
			</div>
			
			<!-- media -->
			<div class="sotab-box htbox" id="tab3" style="display:none;margin-bottom:-60px;">
			<h2><?php _e('MEDIA', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-image-slash"></i> <?php _e('Find and delete all 404 images in media', 'horse-tools') ?></h3>
				<div class="ht-del">
				<a href="javascript:void(0)" id="delete-media"><i class="fa-regular fa-trash"></i> <?php _e('Delete all 404 images', 'horse-tools'); ?></a>
				</div>	
				<div class="emed" style="display:none"><div class="ht-sload"></div> <?php _e('Please wait', 'horse-tools'); ?></div>
				<div id="del-media"></div>
				<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Delete 404 images that do not exist saved in the database', 'horse-tools'); ?></p>
			   <h3><i class="fa-regular fa-image-slash"></i> <?php _e('Find and delete all 404 thumbnail images in media', 'horse-tools') ?></h3>
				<div class="ht-del">
				<a href="javascript:void(0)" id="delete-media-thum"><i class="fa-regular fa-trash"></i> <?php _e('Delete all 404 thumbnail images', 'horse-tools'); ?></a>
				</div>	
				<div class="emed-thum" style="display:none"><div class="ht-sload"></div> <?php _e('Please wait', 'horse-tools'); ?></div>
				<div id="del-media-thum"></div>
				<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Delete 404 thumbnails that do not exist and are stored in the database', 'horse-tools'); ?></p>
			  <h3><i class="fa-regular fa-image-slash"></i> <?php _e('Delete cropped image', 'horse-tools') ?></h3>	
				<div class="ht-card-note ht-del-crop"> 
				<?php
				global $_wp_additional_image_sizes;
				$image_sizes = get_intermediate_image_sizes();
				if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes) > 0) {
					$image_sizes = array_merge($image_sizes, array_keys($_wp_additional_image_sizes));
				}
				$image_sizes = array_unique($image_sizes);
				$selected_sizes = array(); 
				foreach ($image_sizes as $i => $size) {
					$width = isset($_wp_additional_image_sizes[$size]['width']) ? $_wp_additional_image_sizes[$size]['width'] : get_option($size.'_size_w');
					?>
					<p>
						<a href="javascript:void(0)" id="cropdel-<?php echo $size; ?>"><i class="fa-regular fa-trash"></i> <?php echo $size .' (W: '. $width .')'; ?></a>
					</p>
				<?php } ?>
				</div>
				<div id="delete-size-end"></div>
				<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Exercise caution when using this feature, as your interface may require various sizes to display properly. If youre a web designer, youll understand the need for a variety of sizes to be utilized', 'horse-tools'); ?></p>
			</div>
			</div>
	
		</div>
	  </div>
	  <div class="ht-sidebar">
	  </div>
	</div>  
	</div>
	<script>
		jQuery(document).ready(function($) {
			// xoa revisions
			$('#delete-revisions').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_post_revisions'); ?>';
				$('.edel').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'horsetools_delete_revisions',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result').html('<span><?php _e('All revisions have been deleted', 'horse-tools'); ?></span>');
						$('.edel').hide();
					},
					error: function(response) {
						$('#del-result').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
					}
				});
			});
			// xoa auto-drafts
			$('#delete-auto-drafts').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_post_drafts'); ?>';
				$('.edel').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'horsetools_delete_auto_drafts',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result').html('<span><?php _e('All autosaves have been deleted', 'horse-tools'); ?></span>');
						$('.edel').hide();
					},
					error: function(response) {
						$('#del-result').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
					}
				});
			});
			// xoa tat ca trong thung rac
			$('#delete-all-trashed-posts').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_post_trashed'); ?>';
				$('.edel').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'horsetools_delete_all_trashed_posts',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result').html('<span><?php _e('All items in the trash have been deleted', 'horse-tools'); ?></span>');
						$('.edel').hide();
					},
					error: function(response) {
						$('#del-result').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
					}
				});
			});
			// xoa binh luan cho
			$('#delete-comen-pend').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_del_comenpend_nonce'); ?>';
				$('.edel2').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'horsetools_del_comenpend',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result2').html('<span><?php _e('Delete pending comment: ', 'horse-tools'); ?>' + response.data.deleted_count + '</span>');
						$('.edel2').hide();
					},
					error: function(response) {
						$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
					}
				});
			});
			// xoa binh luan spam
			$('#delete-comen-spam').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_del_comenspam_nonce'); ?>';
				$('.edel2').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'horsetools_del_comenspam',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result2').html('<span><?php _e('Delete spam comments: ', 'horse-tools'); ?>' + response.data.deleted_count + '</span>');
						$('.edel2').hide();
					},
					error: function(response) {
						$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
					}
				});
			});
			// xoa binh luan thung rac
			$('#delete-comen-trash').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_del_comentrash_nonce'); ?>';
				$('.edel2').show();
				event.preventDefault();
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: {
						action: 'horsetools_del_comentrash',
						security: ajax_nonce,
					},
					success: function(response) {
						$('#del-result2').html('<span><?php _e('Delete comments in trash: ', 'horse-tools'); ?>' + response.data.deleted_count + '</span>');
						$('.edel2').hide();
					},
					error: function(response) {
						$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
					}
				});
			});
			// xoa binh luan chua link
			$('#delete-comen-link').click(function(event) {
				var horsetoolscl = prompt('<?php _e('Enter from horsetools to confirm deletion:', 'horse-tools') ?>');
				if (horsetoolscl === 'horsetools') {
					var ajax_nonce = '<?php echo wp_create_nonce('horsetools_del_comenlink_nonce'); ?>';
					$('.edel2').show();
					event.preventDefault();
					$.ajax({
						url: '<?php echo admin_url('admin-ajax.php');?>',
						type: 'POST',
						data: {
							action: 'horsetools_del_comenlink',
							security: ajax_nonce,
						},
						success: function(response) {
							$('#del-result2').html('<span><?php _e('Delete comments containing links: ', 'horse-tools'); ?>' + response.data.deleted_count + '</span>');
							$('.edel2').hide();
						},
						error: function(response) {
							$('#del-result2').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
						}
					});
				} else {
					alert('<?php _e('Entering incorrect content', 'horse-tools') ?>');
				}
			});
			// xoa anh 404
			$('#delete-media').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_media_del'); ?>';
				$('.emed').show();
				event.preventDefault();
				$.ajax({
				url: '<?php echo admin_url('admin-ajax.php');?>',
				type: 'POST',
				data: {
					action: 'horsetools_delete_media',
					security: ajax_nonce,
				},
				success: function(response) {
					$('#del-media').html('<span><?php _e('Number of images 404 deleted: ', 'horse-tools'); ?>'+ response.data.deleted_count +'</span>');
					$('.emed').hide();
				},
				error: function(response) {
					$('#del-media').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
				}
				});
			});
			// xoa anh 404 thum
			$('#delete-media-thum').click(function(event) {
				var ajax_nonce = '<?php echo wp_create_nonce('horsetools_media_thum_del'); ?>';
				$('.emed-thum').show();
				event.preventDefault();
				$.ajax({
				url: '<?php echo admin_url('admin-ajax.php');?>',
				type: 'POST',
				data: {
					action: 'horsetools_delete_media_thum',
					security: ajax_nonce,
				},
				success: function(response) {
					$('#del-media-thum').html('<span><?php _e('Number of images 404 deleted: ', 'horse-tools'); ?>'+ response.data.deleted_count +'</span>');
					$('.emed-thum').hide();
				},
				error: function(response) {
					$('#del-media-thum').html('<span><?php _e('Error! Unable to delete', 'horse-tools'); ?></span>');
				}
				});
			});
			// xoa hinh anh crop
			$('a[id^="cropdel-"]').on('click', function(e) {
				e.preventDefault();
				var size = $(this).attr('id').replace('cropdel-', '');
				var horsetoolsInput = prompt('<?php _e('Enter from horsetools to confirm deletion:', 'horse-tools') ?>');
				if (horsetoolsInput === 'horsetools') {
					var data = {
						'action': 'horsetools_delete_images_by_size',
						'size': size,
						'security': '<?php echo wp_create_nonce('horsetools_delete_crop_nonce'); ?>',
					};
					$.ajax({
						type: 'POST',
						url: '<?php echo admin_url('admin-ajax.php');?>',
						data: data,
						success: function(response) {
							$('#delete-size-end').html('<span>'+ response.data + '</span>');
						}
					});
				} else {
					alert('<?php _e('Entering incorrect content', 'horse-tools') ?>');
				}
			});
		});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_clean_options_link() {
	add_submenu_page ('horsetools-options', 'Clean', '<i class="fa-regular fa-broom-wide" style="width:20px;"></i> '. __('Clean', 'horse-tools'), 'manage_options', 'horsetools-clean-options', 'horsetools_clean_options_page');
}
add_action('admin_menu', 'horsetools_clean_options_link');



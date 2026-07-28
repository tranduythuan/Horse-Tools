<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_gindex_options_page() {
	global $horsetools_gindex_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-hand-point-up"></i> <?php _e('INDEX', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-gear"></i> <?php _e('SETTING', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_gindex_settings_group'); ?> 
			<!-- INDEX -->
			<div class="sotab-box htbox" id="tab1" style="margin-bottom:-60px;">
			<h2><?php _e('INDEX', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-hand-point-up"></i> <?php _e('Enter manually', 'horse-tools') ?></h3>
			    
				<?php 
				// bo dem requet tông
				if (is_array($horsetools_gindex_options) || is_object($horsetools_gindex_options)) {
					$count = 0; 
					foreach ($horsetools_gindex_options as $key => $value) {
						if (preg_match('/^json(\d+)$/', $key, $matches)) {
							$count++;
						}
					}
					$data_t = $count * 200;
					$data_i = !empty(get_transient('horsetools_index_count')) ? get_transient('horsetools_index_count') : 0;
					if ($data_i >= $data_t) {
						$data_full = 0;
					} else {
						$data_full = $data_t - $data_i;
					}

					echo '<div class="ht-index-count">';
					echo '<span>'. __('Total:', 'horse-tools') .' '. $data_t .'</span>';
					echo '<span>'. __('Use:', 'horse-tools') .' '. $data_i .'</span>';
					echo '<span>'. __('Still:', 'horse-tools') .' '. $data_full .'</span>';
					echo '</div>';
				}
				?>
				<textarea style="height:500px" class="ht-code-textarea" name="horsetools_gindex_settings[url]" placeholder="<?php _e('Enter the url', 'horse-tools'); ?>"></textarea>
				<div class="ht-index-nut">
					<span class="index-action" data-action="update"><i class="fa-regular fa-play"></i> <?php _e('INDEX', 'horse-tools'); ?></span>
					<span class="index-action" data-action="delete"><i class="fa-regular fa-trash"></i> <?php _e('DEL', 'horse-tools'); ?></span>
					<span class="index-action-check" ><i class="fa-regular fa-circle-check"></i> <?php _e('CHECK', 'horse-tools'); ?></span>
				</div>
				<div class="emed" style="display:none"><div class="ht-sload"></div> <?php _e('Please wait', 'horse-tools'); ?></div>
				<div id="index-bao"></div>
			</div>	
			</div>
			<!-- SETTING -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('SETTING', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-gear"></i> <?php _e('Configure Google index API', 'horse-tools') ?></h3>
				<?php 
				$args = array(
				'public'   => true,
				);
				$post_types = get_post_types($args, 'objects'); 
				foreach ($post_types as $post_type_object) {
					if ($post_type_object->name == 'attachment') {
						continue;
					}
					?>
					<label class="nut-switch">
						<input type="checkbox" name="horsetools_gindex_settings[posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($horsetools_gindex_options['posttype']) && in_array($post_type_object->name, $horsetools_gindex_options['posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ht-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('The "Index Now" link will be added in the custom post management section to allow quick index submission', 'horse-tools'); ?></p>
				<h4><?php _e('Add Google json code', 'horse-tools') ?></h4>
				<div id="sortable-list">
				<div data-id="1" class="ui-state-default ht-button-grid">
				<textarea style="height:200px" class="ht-code-textarea" name="horsetools_gindex_settings[json1]" placeholder="<?php _e('Enter json', 'horse-tools'); ?>"><?php if(!empty($horsetools_gindex_options['json1'])){echo esc_textarea($horsetools_gindex_options['json1']);} ?></textarea>
				</div>
				<?php
				if (is_array($horsetools_gindex_options) || is_object($horsetools_gindex_options)) {
					foreach ($horsetools_gindex_options as $key => $value) {
						if (preg_match('/^json(\d+)$/', $key, $matches) && $matches[1] != 1) {
							$n = $matches[1];
							echo '<div data-id="' . $n . '" class="ui-state-default ht-button-grid">';
							echo '<textarea style="height:200px" class="ht-code-textarea" placeholder="'. __('Enter json', 'horse-tools') .'" type="text" name="horsetools_gindex_settings[json' . $n . ']">' . sanitize_text_field($horsetools_gindex_options['json' . $n]) . '</textarea>';
							echo '<span id="ht-chatx">&#x2715</span>';
							echo '</div>';
						}
					}
				}
				?>
				</div>
				<span id="ht-chatmore"><i class="fa-regular fa-plus"></i> <?php _e('Add json', 'horse-tools'); ?></span>
			</div>
			<div class="ht-submit">
				<button type="submit"><i class="fa-regular fa-floppy-disk"></i> <?php _e('SAVE CONTENT', 'horse-tools'); ?></button>
			</div>
			</div>
			</form>
		</div>
	  </div>
      <div class="ht-sidebar">
	  </div>
	</div>	
	</div>
	<script>
        jQuery(document).ready(function($) {
			// ajax select
			$('form input[type="checkbox"]').change(function() {
				var currentForm = $(this).closest('form');
				$.ajax({
					type: 'POST',
					url: currentForm.attr('action'), 
					data: currentForm.serialize(), 
					success: function(response) {
						console.log('Turn on successfully');
					},
					error: function() {
						console.log('Error in AJAX request');
					}
				});
			});
			// index now and del index
			$('.index-action').click(function() {
				$('.emed').show();
				var links = $('textarea[name="horsetools_gindex_settings[url]"]').val().split('\n');
				var ajax_action = $(this).data('action');
				links.forEach(function(link) {
					idexnowRequest(ajax_action, link.trim()); 
				});
			});
			function idexnowRequest(action, url) {
				var data = {
					action: 'horsetools_index_now_ajax',
					url: url,
					ajax_action: action, 
					ajax_nonce: '<?php echo wp_create_nonce('horsetools_index_now_nonce'); ?>'
				};
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: data,
					success: function(response) {
						$('#index-bao').prepend(response);
						$('#index-bao').addClass('index-bao1');
						$('.emed').hide();
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}
			// index status
			$('.index-action-check').click(function() {
				$('.emed').show();
				var links = $('textarea[name="horsetools_gindex_settings[url]"]').val().split('\n');
				links.forEach(function(link) {
					indexstatusRequest(link.trim()); 
				});
			});
			function indexstatusRequest(url) {
				var data = {
					action: 'horsetools_index_status_ajax',
					url: url,
					ajax_nonce: '<?php echo wp_create_nonce('horsetools_index_status_nonce'); ?>'
				};
				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					type: 'POST',
					data: data,
					success: function(response) {
						$('#index-bao').prepend(response);
						$('#index-bao').addClass('index-bao2');
						$('.emed').hide();
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}
			// them json
			var count = 0;
			$('#ht-chatmore').click(function() {
				var count = $('#sortable-list .ui-state-default:last').data('id') + 1;
				var newDiv = $('<div data-id="' + count + '" class="ui-state-default ht-button-grid">' +
					'<textarea style="height:200px" class="ht-code-textarea" placeholder="<?php _e('Enter json', 'horse-tools'); ?>" name="horsetools_gindex_settings[json' + count + ']"></textarea>' +
					'<span id="ht-chatx">&#x2715</span>' +
					'</div>');
				$('#sortable-list').append(newDiv);
			});
			$('#sortable-list').on('click', '#ht-chatx', function() {
				$(this).parent('.ui-state-default').remove();
				count--;
			});
		});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_gindex_options_link() {
	add_submenu_page ('horsetools-options', 'Index', '<i class="fa-regular fa-hand-point-up" style="width:20px;"></i> '. __('Index now', 'horse-tools'), 'manage_options', 'horsetools-gindex-options', 'horsetools_gindex_options_page');
}
add_action('admin_menu', 'horsetools_gindex_options_link');
function horsetools_gindex_register_settings() {
	register_setting( 'horsetools_gindex_settings_group', 'horsetools_gindex_settings', array( 'sanitize_callback' => 'horsetools_sanitize_gindex' ) );
}
add_action('admin_init', 'horsetools_gindex_register_settings');
// clear cache
function horsetools_gindex_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_gindex_settings', 'options');
}
add_action('update_option_horsetools_gindex_settings', 'horsetools_gindex_settings_cache', 10, 2);


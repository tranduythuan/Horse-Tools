<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_search_options; ?>
			<div class="ht-card">
			  <h3><i class="ti ti-search"></i> <?php _e('Quick search', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'main-search1', __( 'Enable quick search', 'horse-tools' ), array(
					'module'  => 'search',
					'tab'     => 'HORSE SEARCH',
					'section' => 'Quick search',
				) ); ?>
				<div class="tb-doi" id="tb-doi-sogiay" style="display:none"><div class="ht-sload"></div> <?php _e('Automatic initialization after <span id="sogiay" style="padding: 5px;">3</span>s', 'horse-tools'); ?></div>
				<p>
				<input class="ht-input-small" name="horsetools_search_settings[main-search-c1]" type="number" placeholder="10" value="<?php if(!empty($horsetools_search_options['main-search-c1'])){echo sanitize_text_field($horsetools_search_options['main-search-c1']);} ?>"/>
				<label class="ht-label-right"><?php _e('Number of items displayed', 'horse-tools'); ?></label>
				</p>
				
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enter the number of posts or products to be displayed when searching', 'horse-tools'); ?></p>
				
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_search_settings[main-search-c2]" type="text" data-coloris value="<?php if(!empty($horsetools_search_options['main-search-c2'])){echo sanitize_text_field($horsetools_search_options['main-search-c2']);} ?>"/>
				<label class="ht-right-text"><?php _e('Main color', 'horse-tools'); ?></label>
				</p>
				
				<p>
				<?php $styles = array('Light', 'Dark'); ?>
				<select name="horsetools_search_settings[main-search-s1]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($horsetools_search_options['main-search-s1']) && $horsetools_search_options['main-search-s1'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ht-right-text"><?php _e('Light / Dark', 'horse-tools'); ?></label>
				</p>
				
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Change the color of the search box to your preferences', 'horse-tools'); ?></p>
				
				<h4><?php _e('Use shortcodes', 'horse-tools'); ?></h4>
				<?php horsetools_toggle( 'main-search-code1', __( 'Enable shortcodes', 'horse-tools' ), array(
					'module'  => 'search',
					'tab'     => 'HORSE SEARCH',
					'section' => 'Quick search',
				) ); ?>

				<p><input class='ht-input-big ht-view-in' type='text' value='[horsesearch]'/></p>

				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_search_settings[main-search-code2]" type="text" data-coloris value="<?php if(!empty($horsetools_search_options['main-search-code2'])){echo sanitize_text_field($horsetools_search_options['main-search-code2']);} ?>"/>
				<label class="ht-right-text"><?php _e('Icon color', 'horse-tools'); ?></label>
				</p>
				
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You can use the shortcode to add the search icon to the location you want', 'horse-tools'); ?></p>
				
				<h4><?php _e('Create custom post type data', 'horse-tools'); ?></h4>
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
						<input type="checkbox" name="horsetools_search_settings[main-search-posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($horsetools_search_options['main-search-posttype']) && in_array($post_type_object->name, $horsetools_search_options['main-search-posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ht-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<div class="save-json">
				<a href="javascript:void(0)" id="save-json"><i class="ti ti-database"></i> <?php _e('Generate data', 'horse-tools'); ?></a>
				<a href="javascript:void(0)" id="delete-json-folder"><i class="ti ti-trash"></i> <?php _e('Delete data', 'horse-tools'); ?></a>
				</div> 
				<div id="tb-json"></div>
				<div class="tb-doi" id="tb-doi" style="display:none"><div class="ht-sload"></div> <span id="starprocess"></span></div>
				<script>
				jQuery(document).ready(function($) {
					function searchToggle() {
						if ($('input[name="horsetools_search_settings[main-search1]"]').is(':checked')) {
							$('.save-json').css('opacity', '1'); 
							$('.save-json').css('pointer-events', 'auto'); 
						} else {
							$('.save-json').css('opacity', '0.3'); 
							$('.save-json').css('pointer-events', 'none');
						}
					}
					searchToggle();
					$('input[name="horsetools_search_settings[main-search1]"]').change(function() {
						searchToggle();
					});
				});
				jQuery(document).ready(function($) {
						$('input[name="horsetools_search_settings[main-search1]"]').change(function() {
							if ($(this).is(':checked')) {
								$('#tb-doi-sogiay').show();
								var $targetCheckbox = $('input[name="horsetools_search_settings[main-search-posttype][]"]').prop('checked', false);
								if ($targetCheckbox.length > 0) {
									$targetCheckbox.prop('checked', true);
									var countdown = 3;
										var countdownInterval = setInterval(function() {
											$('#sogiay').text(countdown);
											countdown--;
											if (countdown < 0) {
												clearInterval(countdownInterval);
												// $('#save-json').trigger('click');
												$('#tb-doi-sogiay').hide();
												// $('html, body').animate({
												//    scrollTop: $('#save-json').offset().top
												// }, 1000);
											}
										}, 1000);
								}
							}else{
								$('input[name="horsetools_search_settings[main-search-posttype][]"]').prop('checked', false);
							}
						});
				});
				jQuery(document).ready(function($){
					jQuery(document).ready(function($){
					$('#save-json').on('click', function() {
						$('#tb-doi').show();
						var ajax_nonce = '<?php echo wp_create_nonce('horsetools_search_get'); ?>';
						var page = 1;
						var sopost = 0;
						function callAjax() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: {
									action: 'horsetools_json_get',
									security: ajax_nonce,
									page: page
								},
								success: function(response) {
									var jsonResponse = JSON.parse(response);
									if (jsonResponse.page === -1) {
										$('#loadbarprocess').html('<span><?php _e("Number of data completed: '+sopost+'", "horse-tools"); ?></span>');
										$('#tb-doi').hide();
									} else {
										sopost = jsonResponse.count;
										var html = '<span><?php _e("Please wait: '+sopost+'", "horse-tools"); ?></span>';
										$('#starprocess').html(html);
										page = jsonResponse.page;
										callAjax();
									}
								}
							});
						}
						callAjax();
					});
					$('#delete-json-folder').on('click', function() {
						var ajax_nonce = '<?php echo wp_create_nonce('horsetools_search_del'); ?>'; 
						function callAjax() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: {
									action: 'horsetools_json_del', 
									security: ajax_nonce
								},
								success: function(response) {
									$('#loadbarprocess').html('<span><?php _e("Deletion successful", "horse-tools"); ?></span>'); 
								}
							});
						}
						callAjax();
					});	
				});	
				});
				</script>
				<div id="loadbarprocess"></div>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Configure the options and create search data. If you want to refresh, you can delete the search data and recreate it. After enabling quick search and completing data creation, a quick search popup will appear when you enter the search box on the website', 'horse-tools'); ?></p>
			</div>

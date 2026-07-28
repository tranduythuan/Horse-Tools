<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
	function horsetools_export_options_page() {
	global $horsetools_extend_options;
	$combined_settings = array(
		'tool' => get_option('horsetools_settings')
	);
	$option_keys = array(
		'code' => 'horsetools_code_settings',
		'clean' => 'horsetools_extend_settings',
		'font' => 'horsetools_fontset_settings',
		'redirect' => 'horsetools_redirects_settings',
		'index' => 'horsetools_gindex_settings',
		'toc' => 'horsetools_toc_settings',
		'ads' => 'horsetools_ads_settings',
		'notify' => 'horsetools_notify_settings',
		'shortcode' => 'horsetools_shortcode_settings',
		'search' => 'horsetools_search_settings',
		'debug' => 'horsetools_debug_settings'
	);
	foreach ($option_keys as $key => $option_name) {
		if (isset($horsetools_extend_options[$key])) {
			$combined_settings[$key] = get_option($option_name);
		}
	}
	$textarea = esc_textarea(base64_encode(json_encode($combined_settings)));
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-gear"></i> <?php _e('HORSETOOLS', 'horse-tools'); ?></button>
		</div>
		<div class="ht-main">
			<?php
			if (isset($_POST['horsetools_import_tool']) && !empty($_POST['horsetools_export_tool'])) {
				if ( ! current_user_can('manage_options') ) {
					wp_die( esc_html__('You do not have permission to do this.', 'horse-tools') );
				}
				check_admin_referer('horsetools_import', 'horsetools_import_nonce');
				$imported_config = json_decode(base64_decode(stripslashes($_POST['horsetools_export_tool'])), true);
				if ($imported_config && is_array($imported_config)) {
					$option_keys = array(
						'tool' => 'horsetools_settings',
						'code' => 'horsetools_code_settings',
						'clean' => 'horsetools_extend_settings',
						'font' => 'horsetools_fontset_settings',
						'redirect' => 'horsetools_redirects_settings',
						'index' => 'horsetools_gindex_settings',
						'toc' => 'horsetools_toc_settings',
						'ads' => 'horsetools_ads_settings',
						'notify' => 'horsetools_notify_settings',
						'shortcode' => 'horsetools_shortcode_settings',
						'search' => 'horsetools_search_settings',
						'debug' => 'horsetools_debug_settings'
					);
					foreach ($option_keys as $key => $option_name) {
						if (isset($imported_config[$key]) && is_array($imported_config[$key])) {
							if (get_option($option_name) === false) {
								add_option($option_name, $imported_config[$key]);
							} else {
								update_option($option_name, $imported_config[$key]);
							}
						}
					}
					echo '<div class="ht-updated">' . __('New configuration has been successfully added', 'horse-tools') . '</div>';
				} else {
					echo '<div class="ht-updated">' . __('Invalid data', 'horse-tools') . '</div>';
				}
			}
			?>
			<!-- Xuất nhập tool -->
			<div class="sotab-box htbox" id="tab1">
			<form method="post" action="<?php echo menu_page_url('horsetools-export-options', false); ?>">
			<?php wp_nonce_field('horsetools_import', 'horsetools_import_nonce'); ?>
			<h2><?php _e('HORSETOOLS', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-download"></i> <?php _e('Export', 'horse-tools') ?></h3>
				<p>
				<textarea style="height:250px" class="ht-code-textarea" id="horsetools-json"><?php echo $textarea; ?></textarea>
				</p>
				<button type="button" id="horsetools-dow-json"><?php _e('Download', 'horse-tools'); ?></button>
			  <h3><i class="fa-regular fa-upload"></i> <?php _e('Import', 'horse-tools') ?></h3>
				<p>
				<textarea style="height:250px" class="ht-code-textarea" id="horsetools-import-json" name="horsetools_export_tool" placeholder="<?php _e('Enter data here', 'horse-tools'); ?>"></textarea>
				</p>
				<input type="file" id="horsetools-upload-json" accept=".json" style="display:none;" />
				<button type="button" id="horsetools-upload-button" ><?php _e('Upload', 'horse-tools'); ?></button>
			</div>
			<div class="ht-submit">
				<button type="submit" name="horsetools_import_tool"><i class="fa-regular fa-file-import"></i> <?php _e('IMPORT HORSETOOLS DATA', 'horse-tools'); ?></button>
			</div>
			</form>
			</div>
		</div>
	  </div>
	  <div class="ht-sidebar">
	  </div>
	</div>  
	</div>
	<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		// xuat
		const downloadButton = document.getElementById('horsetools-dow-json');
		const etextarea = document.getElementById('horsetools-json');
		downloadButton.addEventListener('click', function() {
			const data = etextarea.value;
			if (data.trim() !== '') {
				const currentDate = new Date();
				const year = currentDate.getFullYear();
				const month = String(currentDate.getMonth() + 1).padStart(2, '0'); 
				const day = String(currentDate.getDate()).padStart(2, '0'); 
				const formattedDate = `${year}-${month}-${day}`;
				const blob = new Blob([data], { type: 'application/json' });
				const url = URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.href = url;
				a.download = `horsetools-${formattedDate}.json`;
				a.style.display = 'none';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
				URL.revokeObjectURL(url);
			} else {
				alert('<?php _e('The textarea is empty', 'horse-tools'); ?>');
			}
		});
		// nhap
		const uploadButton = document.getElementById('horsetools-upload-button');
		const uploadInput = document.getElementById('horsetools-upload-json');    
		const itextarea = document.getElementById('horsetools-import-json');
		uploadButton.addEventListener('click', function() {
			uploadInput.click();
		});
		uploadInput.addEventListener('change', function(event) {
			const file = event.target.files[0]; 
			if (file && file.type === 'application/json') {
				const reader = new FileReader();
				reader.onload = function(e) {
					try {
						const jsonContent = e.target.result;
						itextarea.value = jsonContent;
					} catch (err) {
						alert('<?php _e('The uploaded file is not a valid JSON format', 'horse-tools'); ?>');
					}
				};
				reader.readAsText(file);
			} else {
				alert('<?php _e('Please upload a valid JSON file', 'horse-tools'); ?>');
			}
			uploadInput.value = '';
		});
	});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_export_options_link() {
	add_submenu_page ('horsetools-options', 'Export', '<i class="fa-regular fa-file-export" style="width:20px;"></i> '. __('Export', 'horse-tools'), 'manage_options', 'horsetools-export-options', 'horsetools_export_options_page');
}
add_action('admin_menu', 'horsetools_export_options_link');



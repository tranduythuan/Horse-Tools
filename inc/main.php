<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# add font Awesome
function horsetools_fontawe_assets(){
	global $horsetools_options;
	if (isset($horsetools_options['main-add1'])){
	wp_enqueue_style( 'horsetools-icon', HORSETOOLS_URL . 'font/css/all.css', array(), HORSETOOLS_VERSION);
	}
}
add_action('wp_enqueue_scripts', 'horsetools_fontawe_assets');
# them hieu ung cho trang web như noel
function horsetools_add_hover_style(){
	global $horsetools_options;
	if(isset($horsetools_options['main-hover1']) && $horsetools_options['main-hover1'] == 'Snow1'){
	wp_enqueue_script( 'hover', HORSETOOLS_URL . 'link/hover/hover-style-1.js', array(), HORSETOOLS_VERSION);
	}
	
	if(isset($horsetools_options['main-hover1']) && $horsetools_options['main-hover1'] == 'Snow2'){
	wp_enqueue_script( 'hover', HORSETOOLS_URL . 'link/hover/hover-style-2.js', array(), HORSETOOLS_VERSION);
	}
	
	if(isset($horsetools_options['main-hover1']) && $horsetools_options['main-hover1'] == 'Lunar1'){
	wp_enqueue_script( 'hover', HORSETOOLS_URL . 'link/hover/hover-style-lunar-1.js', array(), HORSETOOLS_VERSION);
	}
	
	if(isset($horsetools_options['main-hover1']) && $horsetools_options['main-hover1'] == 'Lunar2'){
	wp_enqueue_script( 'hover', HORSETOOLS_URL . 'link/hover/hover-style-lunar-2.js', array(), HORSETOOLS_VERSION);
	}
	
	if(isset($horsetools_options['main-hover1']) && $horsetools_options['main-hover1'] == 'Vietnam'){
	wp_enqueue_script( 'hover', HORSETOOLS_URL . 'link/hover/hover-style-vietnam.js', array(), HORSETOOLS_VERSION);
	}
	
	if(isset($horsetools_options['main-hover1']) && $horsetools_options['main-hover1'] == 'Indonesia'){
	wp_enqueue_script( 'hover', HORSETOOLS_URL . 'link/hover/hover-style-indonesia.js', array(), HORSETOOLS_VERSION);
	}
} 
add_action('wp_footer', 'horsetools_add_hover_style');
# che do darkmode
if (isset($horsetools_options['main-mode1'])){
function horsetools_darkmode_icon1(){
	$icon = '<span id="ht-darkmode-toggle" class="ht-sunmode">
		<svg id="ht-icon-moon" xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 512 512"><path fill="currentColor" d="M264 480A232 232 0 0 1 32 248c0-94 54-178.28 137.61-214.67a16 16 0 0 1 21.06 21.06C181.07 76.43 176 104.66 176 136c0 110.28 89.72 200 200 200c31.34 0 59.57-5.07 81.61-14.67a16 16 0 0 1 21.06 21.06C442.28 426 358 480 264 480Z"/></svg>
		<svg id="ht-icon-sun" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 472 472"><path fill="currentColor" d="m123 95l-30 30l-39-38l30-30zM64 216v43H0v-43h64zM256 4v63h-43V4h43zm159 83l-38 38l-30-30l38-38zm-69 292l30-29l39 38l-30 30zm59-163h64v43h-64v-43zM235 109q53 0 90.5 37.5T363 237t-37.5 90.5T235 365t-90.5-37.5T107 237t37.5-90.5T235 109zm-22 362v-63h43v63h-43zM54 388l39-39l30 30l-39 39z"/></svg>
		</span>';
	return $icon;
}
function horsetools_darkmode_icon2(){
    static $counter = 0; 
    $counter++;
    $unique_id = 'ht-darkmode-switch-' . $counter;
    $icon = '<span id="ht-darkmode-toggle-' . $counter . '" class="ht-toggle-switch">
                <input type="checkbox" id="' . $unique_id . '" />
                <label for="' . $unique_id . '" class="ht-switch"></label>
            </span>';
    return $icon;
}
function horsetools_darkmode_js(){
	global $horsetools_options;
	wp_enqueue_script( 'horsedark-js1', HORSETOOLS_URL . 'link/darkmode/horsedark1.js', array(), HORSETOOLS_VERSION);
	if(isset($horsetools_options['main-mode10']) && $horsetools_options['main-mode10'] == 'Toggle'){
		wp_enqueue_script( 'horsedark-js3', HORSETOOLS_URL . 'link/darkmode/horsedark3.js', array(), HORSETOOLS_VERSION, true);
	} elseif (isset($horsetools_options['main-mode10']) && $horsetools_options['main-mode10'] == 'System'){
		wp_enqueue_script( 'horsedark-js4', HORSETOOLS_URL . 'link/darkmode/horsedark4.js', array(), HORSETOOLS_VERSION, true);
	} elseif (isset($horsetools_options['main-mode10']) && $horsetools_options['main-mode10'] == 'Time'){
		wp_enqueue_script( 'horsedark-js5', HORSETOOLS_URL . 'link/darkmode/horsedark5.js', array(), HORSETOOLS_VERSION, true);
	} else {
		wp_enqueue_script( 'horsedark-js2', HORSETOOLS_URL . 'link/darkmode/horsedark2.js', array(), HORSETOOLS_VERSION, true);
	}
}
add_action('wp_enqueue_scripts', 'horsetools_darkmode_js');
function horsetools_darkmode_assets() {
	global $horsetools_options;
	// cau hinh vi tri va mua sac
	$color = horsetools_css_color($horsetools_options['main-mode-c1'] ?? '', '#444444');
	$here = isset($horsetools_options['main-mode11']) && $horsetools_options['main-mode11'] == 'Right' ? 'right' : 'left';
	if(!empty($horsetools_options['main-mode12']) && $horsetools_options['main-mode12'] < 300){
		$bot = horsetools_css_number($horsetools_options['main-mode12'], '30') . 'px';
	} elseif (!empty($horsetools_options['main-mode12']) && $horsetools_options['main-mode12'] >= 300){
		$bot = '50%';
	} else {
		$bot = '30px';
	}
	$lef = !empty($horsetools_options['main-mode13']) ? horsetools_css_number($horsetools_options['main-mode13'], '30') . 'px' : '30px';
	if(isset($horsetools_options['main-mode10']) && $horsetools_options['main-mode10'] == 'Toggle'){ ?>
		<style>
		.ht-toggle-switch input[type="checkbox"] {
				display: none;
		}
		.ht-switch {
			cursor: pointer;
			width: 100%;
			height: 100%;
			background-color: <?php echo $color; ?>;
			border-radius: 20px; 
			transition: background-color 0.3s ease;
			position: relative; 
		}
		.ht-switch::before {
			content: "";
			position: absolute;
			width: 18px; 
			height: 18px; 
			border-radius: 50%;
			background: white;
			z-index:1;
			top: 4px;
			left: 4px;
			transition: transform 0.3s ease;
		}
		.ht-switch:after {
			content: "";
			position: absolute;
			width: 15px; 
			height: 15px; 
			left: 26px;
			top: 50%;
			transform: translateY(-50%);
			background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJjb2xvcjojZmZmZmZmIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGNsYXNzPSJoLWZ1bGwgdy1mdWxsIj48cmVjdCB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgcng9IjMwIiBmaWxsPSJ0cmFuc3BhcmVudCIgc3Ryb2tlPSJ0cmFuc3BhcmVudCIgc3Ryb2tlLXdpZHRoPSIwIiBzdHJva2Utb3BhY2l0eT0iMTAwJSIgcGFpbnQtb3JkZXI9InN0cm9rZSI+PC9yZWN0Pjxzdmcgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgZmlsbD0iI2ZmZmZmZiIgeD0iMCIgeT0iMCIgcm9sZT0iaW1nIiBzdHlsZT0iZGlzcGxheTppbmxpbmUtYmxvY2s7dmVydGljYWwtYWxpZ246bWlkZGxlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxnIGZpbGw9IiNmZmZmZmYiPjxwYXRoIGQ9Ik00MDEuNCAzNTQuMmMtMi45LjEtNS44LjItOC43LjItNDcuOSAwLTkzLTE4LjktMTI2LjgtNTMuNC0zMy45LTM0LjQtNTIuNS04MC4xLTUyLjUtMTI4LjggMC0yNy43IDYuMS01NC41IDE3LjUtNzguNyAzLjEtNi42IDkuMy0xNi42IDEzLjYtMjMuNCAxLjktMi45LS41LTYuNy0zLjktNi4xLTYgLjktMTUuMiAyLjktMjcuNyA2LjhDMTM1LjEgOTUuNSA4MCAxNjguNyA4MCAyNTVjMCAxMDYuNiA4NS4xIDE5MyAxOTAuMSAxOTMgNTggMCAxMTAtMjYuNCAxNDQuOS02OC4xIDYtNy4yIDExLjUtMTMuOCAxNi40LTIxLjggMS44LTMtLjctNi43LTQuMS02LjEtOC41IDEuNy0xNy4xIDEuOC0yNS45IDIuMnoiIGZpbGw9ImN1cnJlbnRDb2xvciI+PC9wYXRoPjwvZz48L3N2Zz48L3N2Zz4=);
			background-size: contain;
			background-repeat: no-repeat;
		}
		.ht-toggle-switch input[type="checkbox"]:checked + .ht-switch:after {
			content: "";
			position: absolute;
			width: 15px; 
			height: 15px; 
			left: 4px;
			top: 50%;
			transform: translateY(-50%);
			background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJjb2xvcjojZmZmZmZmIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGNsYXNzPSJoLWZ1bGwgdy1mdWxsIj48cmVjdCB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgcng9IjAiIGZpbGw9InRyYW5zcGFyZW50IiBzdHJva2U9InRyYW5zcGFyZW50IiBzdHJva2Utd2lkdGg9IjAiIHN0cm9rZS1vcGFjaXR5PSIxMDAlIiBwYWludC1vcmRlcj0ic3Ryb2tlIj48L3JlY3Q+PHN2ZyB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZmZmZiIgeD0iMCIgeT0iMCIgcm9sZT0iaW1nIiBzdHlsZT0iZGlzcGxheTppbmxpbmUtYmxvY2s7dmVydGljYWwtYWxpZ246bWlkZGxlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxnIGZpbGw9IiNmZmZmZmYiPjxwYXRoIGQ9Ik02Ljk5NSAxMmMwIDIuNzYxIDIuMjQ2IDUuMDA3IDUuMDA3IDUuMDA3czUuMDA3LTIuMjQ2IDUuMDA3LTUuMDA3cy0yLjI0Ni01LjAwNy01LjAwNy01LjAwN1M2Ljk5NSA5LjIzOSA2Ljk5NSAxMnpNMTEgMTloMnYzaC0yem0wLTE3aDJ2M2gtMnptLTkgOWgzdjJIMnptMTcgMGgzdjJoLTN6TTUuNjM3IDE5Ljc3OGwtMS40MTQtMS40MTRsMi4xMjEtMi4xMjFsMS40MTQgMS40MTR6TTE2LjI0MiA2LjM0NGwyLjEyMi0yLjEyMmwxLjQxNCAxLjQxNGwtMi4xMjIgMi4xMjJ6TTYuMzQ0IDcuNzU5TDQuMjIzIDUuNjM3bDEuNDE1LTEuNDE0bDIuMTIgMi4xMjJ6bTEzLjQzNCAxMC42MDVsLTEuNDE0IDEuNDE0bC0yLjEyMi0yLjEyMmwxLjQxNC0xLjQxNHoiIGZpbGw9ImN1cnJlbnRDb2xvciI+PC9wYXRoPjwvZz48L3N2Zz48L3N2Zz4=);
			background-size: contain;
			background-repeat: no-repeat;
		}
		.ht-toggle-switch input[type="checkbox"]:checked + .ht-switch {
			background-color: #777;
		}
		.ht-toggle-switch input[type="checkbox"]:checked + .ht-switch::before {
			transform: translateX(18px); 
		}
		</style>
		<?php if (isset($horsetools_options['main-mode-s1'])){ ?>
			<style>
			.ht-toggle-switch {
				background: none;
				border: none;
				cursor: pointer;
				padding: 0;
				width: 44px;
				height: 26px; 
				display: flex;
				align-items: center;
				justify-content: center;
			}
			</style>
		<?php } else { ?>
			<style>
			.ht-toggle-switch {
				background: none;
				border: none;
				cursor: pointer;
				padding: 0;
				position: fixed; 
				bottom: <?php echo $bot; ?>;
				<?php  echo$here; ?>: <?php echo $lef; ?>;
				width: 44px;
				height: 26px; 
				z-index: 1000; 
				display: flex;
				align-items: center;
				justify-content: center;
			}
			</style>
			<?php
			echo horsetools_darkmode_icon2();
		}
	} elseif (isset($horsetools_options['main-mode10']) && $horsetools_options['main-mode10'] == 'System'){
			echo '';
	} elseif (isset($horsetools_options['main-mode10']) && $horsetools_options['main-mode10'] == 'Time'){
			echo '';
	} else {
		if (isset($horsetools_options['main-mode-s1'])){ ?>
			<style>
			#ht-darkmode-toggle {
				padding:3px;
				margin:0px;
				background:none;
				border:none;
				display: flex;
				justify-content: center;
				align-items: center;
				background-color: <?php echo $color; ?>;
				width:25px;
				height:25px;
				border-radius:100%;
				box-sizing: border-box;
			}
			#ht-darkmode-toggle svg {
				width: 15px;
				height: 15px;
			}
			#ht-darkmode-toggle svg path{fill: #fff;}
			</style>	
		<?php } else { ?>
			<style>
			#ht-darkmode-toggle {
				position: fixed;
				bottom: <?php echo $bot; ?>;
				<?php  echo$here; ?>: <?php echo $lef; ?>;
				width: 40px;
				height: 40px;
				border-radius: 50%;
				background-color: <?php echo $color; ?>;
				border: none;
				cursor: pointer;
				display: flex;
				justify-content: center;
				align-items: center;
				box-shadow: 0px 0px 10px #00000075;
				z-index: 9999;
				padding: 10px;
				box-sizing: border-box;
			}
			#ht-darkmode-toggle svg {
				width: 25px;
				height: 25px;
			}
			#ht-darkmode-toggle svg path{fill: #fff;}
			</style>
			<?php echo horsetools_darkmode_icon1();
		}
	}
}
add_action('wp_footer', 'horsetools_darkmode_assets');
// shortcode
function horsetools_darkmode_shortcode($atts) {
    global $horsetools_options;
    if (isset($horsetools_options['main-mode-s1']) && isset($horsetools_options['main-mode10'])) {
        if ($horsetools_options['main-mode10'] != 'System' && $horsetools_options['main-mode10'] != 'Time') {
            if ($horsetools_options['main-mode10'] == 'Toggle') {
                return horsetools_darkmode_icon2();
            } else {
                return horsetools_darkmode_icon1();
            }
        }
    }
    return;
}
add_shortcode('horsedark', 'horsetools_darkmode_shortcode');
}
# custom mau thanh scroll
function horsetools_add_scroll_style(){
	global $horsetools_options; 
	if (isset($horsetools_options['main-scroll1'])){
	$bg = horsetools_css_color($horsetools_options['main-scroll11'] ?? '', 'none');
	$br = horsetools_css_color($horsetools_options['main-scroll12'] ?? '', '#333');
	$ru = !empty($horsetools_options['main-scroll13']) ? horsetools_css_number($horsetools_options['main-scroll13'], '1') .'px' : '1px';
	$zi = !empty($horsetools_options['main-scroll14']) ? horsetools_css_number($horsetools_options['main-scroll14'], '10') .'px' : '10px';
	?>
	<style>
	::-webkit-scrollbar {
		width:<?php echo $zi ?>;
		height:<?php echo $zi ?>;
		background-color:<?php echo $bg ?>;
	}
	::-webkit-scrollbar-thumb {
		background-color:<?php echo $br ?>;
		border-radius:<?php echo $ru ?>;
	}
	::-webkit-scrollbar-track {
		background-color:<?php echo $bg ?>;
		border-radius:<?php echo $ru ?>;
	}
	</style>
	<?php
	}
} 
add_action('wp_head', 'horsetools_add_scroll_style');

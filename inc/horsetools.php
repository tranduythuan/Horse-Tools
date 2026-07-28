<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# an admin khoi ho so
function horsetools_pre_user_hiquery($user_query){
	global $horsetools_options;
	if (isset($horsetools_options['horsetools1']) && !empty($horsetools_options['horsetools11'])){
		$id = intval($horsetools_options['horsetools11']);
		if ( $id <= 0 ) {
			return;
		}
		// The original condition was `is_admin() && current_user_can('manage_options')`,
		// which had the effect exactly backwards: the account was hidden from
		// administrators browsing users.php — the only people who would notice
		// a rogue account — while staying fully visible to anonymous visitors
		// through the REST route /wp-json/wp/v2/users, which is not is_admin().
		//
		// Apply it everywhere instead, and never hide the account from itself
		// so the user can still edit their own profile.
		if ( get_current_user_id() !== $id ) {
			$user_query->query_where .= " AND {$GLOBALS['wpdb']->users}.ID != {$id}";
		}
	}
}
add_action('pre_user_query', 'horsetools_pre_user_hiquery');
function horsetools_get_admin_users() {
    if (function_exists('horsetools_pre_user_hiquery')) {
        remove_action('pre_user_query', 'horsetools_pre_user_hiquery');
    }
    $horseadmins = get_users(array(
        'role' => 'administrator'
    ));
    if (function_exists('horsetools_pre_user_hiquery')) {
        add_action('pre_user_query', 'horsetools_pre_user_hiquery');
    }
    return $horseadmins;
}
# Ẩn horsetools khoi menu
if (isset($horsetools_options['horsetools3'])){
function horsetools_hide_menuadmin(){
		remove_menu_page( 'horsetools-options' );
}
add_action( 'admin_menu', 'horsetools_hide_menuadmin', 999);
} 
# Ẩn plugin khoi quan ly plugin
function horsetools_hide_plugins($plugins) {
    global $horsetools_options;
    $all_plugins = get_plugins(); 
    if (is_array($horsetools_options) || is_object($horsetools_options)) {
        foreach ($horsetools_options as $key => $value) {
            if (preg_match('/^horsetools-pu(\d+)$/', $key, $matches)) {
                $n = $matches[1]; 
                if ($value == 1) {
                    $plugin_keys = array_keys($all_plugins);
                    if (isset($plugin_keys[$n - 1])) { 
                        $plugin_to_hide = $plugin_keys[$n - 1]; 
                        if (isset($plugins[$plugin_to_hide])) {
                            unset($plugins[$plugin_to_hide]); 
                        }
                    }
                }
            }
        }
    }
    return $plugins;
}
add_filter('all_plugins', 'horsetools_hide_plugins');
# xem csdl dung gi
function horsetools_display_db_info() {
    global $wpdb;
    $database_info = $wpdb->get_results("SHOW VARIABLES LIKE 'version'", ARRAY_A);
    if (!empty($database_info)) {
        $db_version = $database_info[0]['Value'];
        $db_type = strpos($db_version, 'MariaDB') !== false ? 'MariaDB' : 'MySQL';
        echo esc_html($db_type) .': <b>'. esc_html($db_version) .'</b>';
    } else {
        echo __('Does not exist', 'horse-tools');
    }
}
# hien thi cac bang dang su dung
function horsetools_display_wp_tables() {
    global $wpdb;
    $default_tables = array(
        'posts',
        'users',
        'comments',
        'terms',
        'term_taxonomy',
        'term_relationships',
        'options',
        'postmeta',
        'usermeta',
        'links',
        'commentmeta',
        'termmeta',
    );
    $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
    if ($tables) {
        echo '<div class="ht-showcsdl">';
        foreach ($tables as $table) {
            $table_name = $table[0];
            $row_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $table_name_without_prefix = substr($table_name, strlen($wpdb->prefix));
            $table_size = $wpdb->get_var("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as table_size_mb FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '$table_name'");
            
            // Check if the table is not default and has 0 rows
            if (!in_array($table_name_without_prefix, $default_tables) && $row_count == 0) {
                echo '<div><span style="color:#00a1c7;">' . esc_html($table_name) . '</span></div>';
            } elseif (in_array($table_name_without_prefix, $default_tables)) {
                echo '<div><span style="color:#ff4444;font-weight:bold">' . esc_html($table_name) . '</span></div>';
            } else {
                echo '<div>' . esc_html($table_name) . '</div>';
            }
            echo '<div>' . esc_html($row_count) . '</div>';
            echo '<div>' . esc_html($table_size) . ' MB</div>';
        }
        echo '</div>';
    }
}

# kiêm tra dung luong database
function horsetools_get_database_size() {
    global $wpdb;
    $total_size = 0;
    $tables = $wpdb->get_results("SHOW TABLE STATUS");
    foreach ($tables as $table) {
        $total_size += $table->Data_length + $table->Index_length;
    }
    $total_size_formatted = size_format($total_size, 2);
    return $total_size_formatted;
}
# Horse Tools brand mark. Inline so it inherits the surrounding colour and
# needs no HTTP request; used for both the menu icon and the panel header.
function horsetools_brand_mark_svg( $fill = 'currentColor' ) {
    $fill = esc_attr( $fill );
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100%" height="100%" role="img" aria-label="Horse Tools">'
        . '<path fill="' . $fill . '" d="M24,86 V48 a26,26 0 0 1 52,0 V86 H62 V48 a12,12 0 0 0 -24,0 V86 Z"/>'
        . '<circle fill="' . $fill . '" cx="29" cy="40" r="3.4"/>'
        . '<circle fill="' . $fill . '" cx="71" cy="40" r="3.4"/>'
        . '<circle fill="' . $fill . '" cx="33" cy="26" r="3.4"/>'
        . '<circle fill="' . $fill . '" cx="67" cy="26" r="3.4"/>'
        . '</svg>';
}

# Tùy chỉnh Logo
function horsetools_logo(){
    global $horsetools_options;
    // The panel logo plate uses the bright gold --logo as its background, so
    // the mark itself is dark ink. (The wp-admin menu icon is the reverse:
    // white on the dark menu bar — see horsetools_icon().)
    $logo = '<span class="ht-brandmark" style="display:inline-block;width:40px;height:40px;">'
        . horsetools_brand_mark_svg( '#3d2a00' ) . '</span>';
    if (isset($horsetools_options['horsetools61'])) {
        switch ($horsetools_options['horsetools61']) {
            case 'icon 1':
                echo $logo;
                break;
            case 'icon 2':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-admin-tools"></span>';
                break;
            case 'icon 3':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-admin-generic"></span>';
                break;
            case 'icon 4':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-image-filter"></span>';
                break;
			case 'icon 5':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-wordpress"></span>';
                break;
			case 'icon 6':
                echo '<span style="font-size:40px;color:#fff;display:contents;" class="dashicons dashicons-shield"></span>';
                break;
            default:
                echo $logo;
                break;
        }
    } else {
        echo $logo;
    }
}
# Tùy chỉnh icon
function horsetools_icon(){
    global $horsetools_options;
    // Menu icons must be a data URI, so the brand mark is encoded here rather
    // than emitted inline. Same artwork as horsetools_brand_mark_svg().
    $icon = 'data:image/svg+xml;base64,' . base64_encode( horsetools_brand_mark_svg( '#ffffff' ) );
	if (isset($horsetools_options['horsetools61'])) {
    switch ($horsetools_options['horsetools61']) {
        case 'icon 1':
            return $icon;
            break;
        case 'icon 2':
            return 'dashicons-admin-tools';
            break;
        case 'icon 3':
            return 'dashicons-admin-generic';
            break;
        case 'icon 4':
            return 'dashicons-image-filter';
            break;
		case 'icon 5':
            return 'dashicons-wordpress';
            break;
		case 'icon 6':
            return 'dashicons-shield';
            break;
        default:
            return $icon;
            break;
    }
	} else {
		return $icon;
	}
}
# custom skin css admin
function horsetools_register_custom_admin_color_scheme() {
    wp_admin_css_color('horse-tools', __('Horse Tools', 'horse-tools'), false,
        array(
            'base'     => '#1f1b16', // warm charcoal, the anchor for the gold
            'focus'    => '#8a6100',
            'current'  => '#e0a500',
            'gradient' => '#c79200',
        )
    );
}
add_action('admin_init', 'horsetools_register_custom_admin_color_scheme');
function horsetools_customskin_admin_css(){
    global $wp_styles;
    $user_id = get_current_user_id();
    $user_color_scheme = get_user_option('admin_color', $user_id);
    if ($user_color_scheme === 'horse-tools') { ?>
    <style>#wpadminbar,.wp-core-ui .button-primary{border-bottom:3px solid #513900}body{background-color:#f8f8f8!important;}::-webkit-scrollbar{width:8px;height:8px;background-color:none}::-webkit-scrollbar-thumb{background-color:#8a6100;border-radius:0}::-webkit-scrollbar-track{background-color:none;border-radius:0}#adminmenu .wp-submenu{border: 1px solid #cccccc4a !important;background-color:#000000d4;border-radius:5px;box-sizing:border-box;}#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,#adminmenu .wp-menu-arrow,#adminmenu .wp-menu-arrow div,#adminmenu li.current a.menu-top,#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu{background:#8a6100;border-bottom:4px solid #513900!important;border-radius:5px;margin-bottom:7px}ul#adminmenu a.wp-has-current-submenu:after,ul#adminmenu>li.current>a.current:after{border-right-color:#272727}#adminmenu,#adminmenu .wp-submenu,#adminmenuback,#adminmenuwrap{width:150px}#adminmenuback{background-color:#272727}#adminmenuback,#adminmenuwrap{padding:7px}#adminmenu,#adminmenu li.wp-menu-open,#adminmenuwrap{background-color:#272727!important}#wpadminbar{background-color:#8a6100;background-image:linear-gradient(135deg,rgba(255,255,255,.05) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.05) 50%,rgba(255,255,255,.05) 75%,transparent 75%,transparent);background-size:20px 20px}#wpadminbar img{border-radius:100%}#collapse-button{background-color:#8c3b00;border-radius:5px;border-bottom:3px solid #4a1f00;color:#fff!important;height:37px}#adminmenu a:focus,#adminmenu a:hover,.folded #adminmenu .wp-submenu-head:hover{box-shadow:none!important}#adminmenu li.wp-menu-separator{margin:0!important;height:0!important}#adminmenu li{margin-top:7px}#adminmenu li.menu-top:hover,#adminmenu li.opensub>a.menu-top,#adminmenu li>a.menu-top:focus{background-color:#8a61005e;color:#fff!important;border-radius:5px}#adminmenu .wp-submenu a:focus,#adminmenu .wp-submenu a:hover,#adminmenu a:hover,#adminmenu li.menu-top>a:focus,#adminmenu li:hover div.wp-menu-image:before{color:#fff!important}#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after,#adminmenu li.wp-has-submenu.wp-not-current-submenu:focus-within:after{border-right-color:#272727}#adminmenu li.menu-top{background-color:#83838338;border-radius:5px}#adminmenu{margin:5px 0!important}@media screen and (max-width:782px){.auto-fold #adminmenu .selected .wp-submenu{margin-top:10px}.auto-fold #adminmenu li.menu-top .wp-submenu>li>a{padding:5px 12px!important}}#wpfooter{background:#fff;border-top:1px solid #8a6100}#adminmenu .awaiting-mod,#adminmenu .menu-counter,#adminmenu .update-plugins{background-color:#c792008f}.widefat tfoot tr td,.widefat tfoot tr th,.widefat thead tr td,.widefat thead tr th{color:#f0f0f1;background:#c79200;border-bottom:3px solid #51390045}.widefat tfoot tr td a,.widefat tfoot tr th a,.widefat thead tr td a,.widefat thead tr th a{color:#f0f0f1}.wrap .add-new-h2,.wrap .add-new-h2:active,.wrap .page-title-action,.wrap .page-title-action:active{border:none!important;background:#8a6100!important;border-bottom:3px solid #513900!important;color:#fff!important}.wp-core-ui .button,.wp-core-ui .button-secondary, .components-button.is-secondary{box-shadow:none;border:none!important;background:#0a0a0a94!important;border-bottom:3px solid #000000a6!important;color:#fff!important}#screen-meta-links .show-settings{border:none!important;background:#e7e7e7!important;border-bottom:3px solid #cfcfcf!important;color:#2f2f2f!important}.media-frame input[type=color],.media-frame input[type=date],.media-frame input[type=datetime-local],.media-frame input[type=datetime],.media-frame input[type=email],.media-frame input[type=month],.media-frame input[type=number],.media-frame input[type=password],.media-frame input[type=search],.media-frame input[type=tel],.media-frame input[type=text],.media-frame input[type=time],.media-frame input[type=url],.media-frame input[type=week],.media-frame select,.media-frame textarea,.wrap select,input[type=color],input[type=date],input[type=datetime-local],input[type=datetime],input[type=email],input[type=month],input[type=number],input[type=password],input[type=search],input[type=tel],input[type=text],input[type=time],input[type=url],input[type=week],select,textarea{border:none;background-color:#e9e9e9;border-bottom: 3px solid #00000017;}.wrap input[type=checkbox],.wrap input[type=radio]{border:1px solid #d7d7d7;border-radius:100%}.postbox .inside h2,.wrap [class$=icon32]+h2,.wrap h1,.wrap>h2:first-child{font-weight:700;color:#513900}#menu-management .menu-edit,#menu-settings-column .accordion-container,.comment-ays,.feature-filter,.manage-menus,.menu-item-handle,.popular-tags,.postbox,.stuffbox,.widget-inside,.widget-top,.widgets-holder-wrap,.wp-editor-container,p.popular-tags,table.widefat,.wp-filter{border:none;box-shadow: 0px 0px 7px #0000003d;}.postbox-header{border-bottom: 1px solid #a97a00;background:#fff}.postbox{border:1px solid #fff !important}.widefat thead td,.widefat thead th{border-bottom:1px solid #c79200}.widefat tfoot td,.widefat tfoot th{border-top:1px solid #c79200;border-bottom:none}#bulk-titles,ul.cat-checklist{border:1px solid #c79200;border-radius:3px;background:#fff;}.alternate,.striped>tbody>:nth-child(odd),ul.striped>:nth-child(odd){background-color:#c792001f;background-image: linear-gradient(135deg, #ffffff8a 25%, transparent 25%, transparent 50%, #ffffff8a 50%, #ffffff8a 75%, transparent 75%, transparent);background-size: 20px 20px;}#wp-content-editor-tools{background-color: #f8f8f8;}div.mce-toolbar-grp,.quicktags-toolbar{background: #ffffff !important;}</style>
    <?php }
}
add_action('admin_head', 'horsetools_customskin_admin_css');
function horsetools_customskin_adminbar_css(){
	global $wp_styles;
    $user_id = get_current_user_id();
    $user_color_scheme = get_user_option('admin_color', $user_id);
	if (is_admin_bar_showing() && $user_color_scheme === 'horse-tools'){ ?>
	<style>
	#wpadminbar{background-color:#8a6100;background-image:linear-gradient(135deg,rgba(255,255,255,.05) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.05) 50%,rgba(255,255,255,.05) 75%,transparent 75%,transparent);background-size:20px 20px;border-bottom: 3px solid #513900;}#wpadminbar img{border-radius:100%}
	</style>
	<?php }
}
add_action('wp_head', 'horsetools_customskin_adminbar_css');
// xoa admin hiden
function horsetools_del_option_admin() {
    if ( !current_user_can('manage_options') ) {
        return;
    }
    if ( isset($_GET['del']) && $_GET['del'] === 'adminhorsetools' ) {
        check_admin_referer('horsetools_del_hidden_admin');
        $horsetools_options = get_option('horsetools_settings', array());
        if ( isset($horsetools_options['horsetools2']) ) {
            $horsetools_options['horsetools2'] = NULL;
            update_option('horsetools_settings', $horsetools_options);
        }
    }
}
add_action('admin_init', 'horsetools_del_option_admin');
// add lang va tuy chon lang
function horsetools_load_textdomain() {
    global $horsetools_options;
    if (isset($horsetools_options['lang']) && $horsetools_options['lang'] == 'English') {
    } elseif (isset($horsetools_options['lang']) && $horsetools_options['lang'] == 'Việt Nam') {
        load_textdomain('horse-tools', HORSETOOLS_DIR . 'lang/horse-tools-vi.mo');
    } elseif (isset($horsetools_options['lang']) && $horsetools_options['lang'] == 'Indonesia') {
		load_textdomain('horse-tools', HORSETOOLS_DIR . 'lang/horse-tools-id_ID.mo');
    } else {
        load_plugin_textdomain('horse-tools', false, dirname(HORSETOOLS_BASE) . '/lang/');
    }
}
add_action('plugins_loaded', 'horsetools_load_textdomain');















<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('GOOGLE', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check11" data-target="play11" type="checkbox" name="horsetools_settings[goo]" value="1" <?php if ( isset($horsetools_options['goo']) && 1 == $horsetools_options['goo'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play11" class="ht-card toggle-div">
  <h3><i class="ti ti-login-2"></i> <?php _e('Sign in with Google account', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'goo-log1', __( 'Enable to use', 'horse-tools' ), array(
		'tab'         => 'GOOGLE',
		'section'     => 'Sign in with Google account',
		'description' => __( 'Enable and configure the functions below to enable Google sign-in to work', 'horse-tools' ),
	) ); ?>
	<p>
	<input class="ht-input-big ht-view-in" type="text" value="<?php echo home_url(); ?>/wp-admin/admin-ajax.php?action=horsetools_login_google"/>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Copy the link below to add it to the Authorized redirect URLs in your Google Developers project', 'horse-tools'); ?><br>
	<a target="_blank" href="https://console.developers.google.com">Google Developers Console</a>
	</p>
	<h4><?php _e('Registration role options', 'horse-tools'); ?></h4>
	<?php
	$roles = get_editable_roles();
	echo '<select name="horsetools_settings[goo-role1]">';
	echo '<option value="">Default</option>';
	foreach ($roles as $role_name => $role_info) {
		if ($role_name != 'administrator' && $role_name != 'editor') {
		if(isset($horsetools_options['goo-role1']) && $horsetools_options['goo-role1'] == $role_name) { $selected = 'selected="selected"'; } else { $selected = NULL; }
		echo '<option value="'. $role_name .'" '. $selected .'>'. $role_name .'</option>';
		}
	}
	echo '</select>';
	?>
	<label class="ht-right-text"><?php _e('Role', 'horse-tools'); ?></label>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You can customize the role of successful registrants, with the default role being "subscriber"', 'horse-tools'); ?></p>
	<h4><?php _e('Add Google API', 'horse-tools'); ?></h4>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Client ID', 'horse-tools'); ?>" name="horsetools_settings[goo-log11]" type="text" value="<?php if(!empty($horsetools_options['goo-log11'])){echo sanitize_text_field($horsetools_options['goo-log11']);} ?>"/>
	</p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Client Secret', 'horse-tools'); ?>" name="horsetools_settings[goo-log12]" type="text" value="<?php if(!empty($horsetools_options['goo-log12'])){echo sanitize_text_field($horsetools_options['goo-log12']);} ?>"/>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Retrieve the API Client ID and Client Secret from your Google Developers project and add them to the two fields above', 'horse-tools'); ?></p>
	
	<h4><?php _e('Display options', 'horse-tools'); ?></h4>
	<p>
	<input class="ht-input-big ht-view-in" type="text" value="[google-login]"/>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You can paste the shortcode into the position where you want the login button to appear', 'horse-tools'); ?></p>
	<?php horsetools_toggle( 'goo-log13', __( 'Display on the default login form', 'horse-tools' ), array(
		'tab'         => 'GOOGLE',
		'section'     => 'Sign in with Google account',
		'description' => __( 'Enable to display the Google login button on the default WordPress login form', 'horse-tools' ),
	) ); ?>

	<?php horsetools_toggle( 'goo-log14', __( 'Display on the WooCommerce login form', 'horse-tools' ), array(
		'tab'         => 'GOOGLE',
		'section'     => 'Sign in with Google account',
		'description' => __( 'Enable to display the Google login button on the WooCommerce login form', 'horse-tools' ),
	) ); ?>
	
	</p>
</div>		
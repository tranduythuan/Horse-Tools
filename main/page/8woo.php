<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('WOOCOMMERCE', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check8" data-target="play8" type="checkbox" name="horsetools_settings[woo]" value="1" <?php if ( isset($horsetools_options['woo']) && 1 == $horsetools_options['woo'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play8" class="ht-card toggle-div">
  <h3><i class="ti ti-text-size"></i> <?php _e('Modify content', 'horse-tools') ?></h3>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Change the Buy Now text on the single product page', 'horse-tools'); ?>" name="horsetools_settings[woo-text1]" type="text" value="<?php if(!empty($horsetools_options['woo-text1'])){echo sanitize_text_field($horsetools_options['woo-text1']);} ?>"/>
	</p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('The text to replace Buy Now on the product page', 'horse-tools'); ?>" name="horsetools_settings[woo-text2]" type="text" value="<?php if(!empty($horsetools_options['woo-text2'])){echo sanitize_text_field($horsetools_options['woo-text2']);} ?>"/>
	</p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Display text if the price is 0', 'horse-tools'); ?>" name="horsetools_settings[woo-text3]" type="text" value="<?php if(!empty($horsetools_options['woo-text3'])){echo sanitize_text_field($horsetools_options['woo-text3']);} ?>"/>
	</p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Display text if out of stock', 'horse-tools'); ?>" name="horsetools_settings[woo-text4]" type="text" value="<?php if(!empty($horsetools_options['woo-text4'])){echo sanitize_text_field($horsetools_options['woo-text4']);} ?>"/>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Modify some information on Woocommerce that you want', 'horse-tools'); ?></p>
  <h3><i class="ti ti-text-size"></i> <?php _e('Customize Currency Unit', 'horse-tools') ?></h3>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Enter units', 'horse-tools'); ?>" name="horsetools_settings[woo-cy1]" type="text" value="<?php if(!empty($horsetools_options['woo-cy1'])){echo sanitize_text_field($horsetools_options['woo-cy1']);} ?>"/>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This feature allows you to customize the currency symbol as desired', 'horse-tools'); ?></p>
	
  <h3><i class="ti ti-brand-telegram"></i> <?php _e('Configure order notifications to be sent to Telegram', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'woo-tele1', __( 'Enable notifications', 'horse-tools' ), array(
		'tab'     => 'WOOCOMMERCE',
		'section' => 'Configure order notifications to be sent to Telegram',
	) ); ?>
	<p><input class="ht-input-big"  placeholder="<?php _e('API Token', 'horse-tools'); ?>" name="horsetools_settings[woo-tele11]" type="text" value="<?php if(!empty($horsetools_options['woo-tele11'])){echo sanitize_text_field($horsetools_options['woo-tele11']);} ?>"/></p>
	<p><input class="ht-input-big"  placeholder="<?php _e('Chat ID', 'horse-tools'); ?>" name="horsetools_settings[woo-tele12]" type="text" value="<?php if(!empty($horsetools_options['woo-tele12'])){echo sanitize_text_field($horsetools_options['woo-tele12']);} ?>"/></p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('With this feature, you can notify your orders to your Telegram group, helping you manage orders conveniently', 'horse-tools'); ?></p>
</div>	
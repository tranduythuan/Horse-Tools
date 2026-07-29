<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('DATABASE', 'horse-tools'); ?></h2>
  <h3><i class="ti ti-database"></i> <?php _e('Your database', 'horse-tools') ?></h3>
	<div class="ht-card-note">
		<div class="ht-showcsdl ht-showcsdl-tit">
			<div><?php _e('Table name', 'horse-tools') ?></div>
			<div><?php _e('Field', 'horse-tools') ?></div>
			<div><?php _e('MB', 'horse-tools') ?></div>
		</div>
		<div class="ht-scdl-scrool">
			<?php horsetools_display_wp_tables(); ?>
		</div>
		<div class="ht-csdl-tatol">
			<p class="cdata-tatol">
			<?php echo __('Total data', 'horse-tools') .': '. esc_html(horsetools_get_database_size()); ?>
			</p>
			<p class="cdata-data">
			 <span id="cdata1"></span> <?php _e('System', 'horse-tools') ?>
			 <span id="cdata2"></span> <?php _e('Empty', 'horse-tools') ?>
			 <span id="cdata3"></span> <?php _e('Data', 'horse-tools') ?>
			</p>
		</div>
	</div>
  
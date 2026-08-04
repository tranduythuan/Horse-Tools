<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
  <h3><i class="ti ti-link"></i> <?php _e('Configure category permalink', 'horse-tools') ?></h3>
	<!-- thay doi slug 1 -->
	<?php horsetools_toggle( 'post-link1', __( 'Remove category slug from permalink', 'horse-tools' ), array(
		'tab'     => 'SEO',
		'section' => 'Configure category permalink',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Convert paths from (domain.com/category/news/latest-news, domain.com/category/news) to (domain.com/latest-news, domain.com/news)', 'horse-tools'); ?><br>
	<b><?php _e('Settings > Permalinks > Save changes', 'horse-tools') ?></b>
	</p>
	<!-- thay doi slug 2 -->
	<?php horsetools_toggle( 'post-link2', __( 'Remove tag slug from permalink', 'horse-tools' ), array(
		'tab'     => 'SEO',
		'section' => 'Configure category permalink',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Convert path from domain.com/tag/news/ to domain.com/news/', 'horse-tools'); ?><br>
	<b><?php _e('Settings > Permalinks > Save changes', 'horse-tools') ?></b>
	</p>

  <h3><i class="ti ti-link"></i> <?php _e('Add .html for page', 'horse-tools') ?></h3>
	<!-- thay doi slug 1 -->
	<?php horsetools_toggle( 'post-html1', __( 'Enable .html for page', 'horse-tools' ), array(
		'tab' => 'SEO',
		'section'     => 'Add .html for page',
		'description' => __( 'If you enable this feature, your Pages will have .html appended to them, for example: domain.com/page.html', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-star"></i> <?php _e('SEO optimization', 'horse-tools') ?></h3>
	<!-- thêm alt bằng tiêu đề bài viết cho ảnh -->
	<?php horsetools_toggle( 'post-alt1', __( 'Use titles as descriptions for images', 'horse-tools' ), array(
		'tab' => 'SEO',
		'section'     => 'SEO optimization',
		'description' => __( 'This feature will use the title of the post as the description for the image when uploaded', 'horse-tools' ),
	) ); ?>
	<!-- them nofollow và _blank cho đường dẫn bên ngoài -->
	<?php horsetools_toggle( 'post-out1', __( 'Add nofollow and _blank for external links', 'horse-tools' ), array(
		'tab' => 'SEO',
		'section'     => 'SEO optimization',
		'description' => __( 'This feature will add nofollow and _blank to external links on your site', 'horse-tools' ),
	) ); ?>


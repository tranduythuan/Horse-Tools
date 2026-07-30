<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('CONTENT', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check6" data-target="play6" type="checkbox" name="horsetools_settings[post]" value="1" <?php if ( isset($horsetools_options['post']) && 1 == $horsetools_options['post'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play6" class="ht-card toggle-div">
  <h3><i class="ti ti-photo"></i> <?php _e('Image function for posts', 'horse-tools') ?></h3>
	<!-- post up hinh anh 1 -->
	<?php horsetools_toggle( 'post-up1', __( 'Save images to media when copying from another source', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Image function for posts',
	) ); ?>
	<!-- post up hinh anh 1 -->
	<?php horsetools_toggle( 'post-up11', __( 'Remove img tag attributes', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Image function for posts',
		'parent'  => 'post-up1',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enable this feature if you want images in posts copied from another source to be stored on your website', 'horse-tools'); ?></p>

	<!-- xoa bai viet xoa hinh anh 1 -->
	<?php horsetools_toggle( 'post-del1', __( 'Enable deleting posts to also delete images', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Image function for posts',
		'warning' => __( 'This function allows you to delete images attached to posts when deleting the posts themselves. Note that if multiple posts use the same image, it will also be deleted when removing the post', 'horse-tools' ),
	) ); ?>

	<!-- anh dau tiên làm ảnh đại diện bài viết -->
	<?php horsetools_toggle( 'post-thum1', __( 'The first image becomes the featured image', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Image function for posts',
	) ); ?>

	<p style="display:flex;">
	<input id="ht-add5" class="ht-input-big" name="horsetools_settings[post-thum11]" type="text" value="<?php if(!empty($horsetools_options['post-thum11'])){echo sanitize_text_field($horsetools_options['post-thum11']);} ?>" placeholder="<?php _e('Add default featured image', 'horse-tools'); ?>" />
	<button class="ht-selec" data-input-id="ht-add5"><?php _e('Select image', 'horse-tools'); ?></button>
	</p>

	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enable this feature if you want the first image in the post to become the featured image if the featured image field is empty. Additionally, you can select a default featured image in case both the featured image and the images in the post are empty', 'horse-tools'); ?></p>

	<!-- đặt ảnh gốc khi thêm vào bài viết -->
	<?php horsetools_toggle( 'post-imgsize1', __( 'The original image size when added to the post', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'Image function for posts',
		'description' => __( 'Enable this feature if you want the original image size to be selected by default whenever adding images to the post', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-copy"></i> <?php _e('Duplicate post, page, custom post type', 'horse-tools') ?></h3>
	<!-- post nhan ban 1 -->
	<?php horsetools_toggle( 'post-dup1', __( 'Add duplicate button', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Duplicate post, page, custom post type',
	) ); ?>
	<h4><?php _e('Custom posts will be displayed', 'horse-tools') ?></h4>
	<p>
	<?php
	$args = array(
    'public'   => true,
	);
	$post_types = get_post_types($args, 'objects');
	foreach ($post_types as $post_type_object) {
		if ($post_type_object->name == 'attachment' || $post_type_object->name == 'product') {
			continue;
		}
		?>
		<label class="nut-switch">
			<input type="checkbox" name="horsetools_settings[post-dup-posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($horsetools_options['post-dup-posttype']) && in_array($post_type_object->name, $horsetools_options['post-dup-posttype'])) echo 'checked="checked"'; ?> />
			<span class="slider"></span>
		</label>
		<label class="ht-label-right"><?php echo $post_type_object->labels->name; ?></label>
		</p>
		<?php
	}
	?>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you want to enable the feature to duplicate posts, pages, or custom post types, please activate this function', 'horse-tools'); ?></p>

  <h3><i class="ti ti-link"></i> <?php _e('Configure category permalink', 'horse-tools') ?></h3>
	<!-- thay doi slug 1 -->
	<?php horsetools_toggle( 'post-link1', __( 'Remove category slug from permalink', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Configure category permalink',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Convert paths from (domain.com/category/news/latest-news, domain.com/category/news) to (domain.com/latest-news, domain.com/news)', 'horse-tools'); ?><br>
	<b><?php _e('Settings > Permalinks > Save changes', 'horse-tools') ?></b>
	</p>
	<!-- thay doi slug 2 -->
	<?php horsetools_toggle( 'post-link2', __( 'Remove tag slug from permalink', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Configure category permalink',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Convert path from domain.com/tag/news/ to domain.com/news/', 'horse-tools'); ?><br>
	<b><?php _e('Settings > Permalinks > Save changes', 'horse-tools') ?></b>
	</p>

  <h3><i class="ti ti-link"></i> <?php _e('Add .html for page', 'horse-tools') ?></h3>
	<!-- thay doi slug 1 -->
	<?php horsetools_toggle( 'post-html1', __( 'Enable .html for page', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'Add .html for page',
		'description' => __( 'If you enable this feature, your Pages will have .html appended to them, for example: domain.com/page.html', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-star"></i> <?php _e('SEO optimization', 'horse-tools') ?></h3>
	<!-- thêm alt bằng tiêu đề bài viết cho ảnh -->
	<?php horsetools_toggle( 'post-alt1', __( 'Use titles as descriptions for images', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'SEO optimization',
		'description' => __( 'This feature will use the title of the post as the description for the image when uploaded', 'horse-tools' ),
	) ); ?>
	<!-- them nofollow và _blank cho đường dẫn bên ngoài -->
	<?php horsetools_toggle( 'post-out1', __( 'Add nofollow and _blank for external links', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'SEO optimization',
		'description' => __( 'This feature will add nofollow and _blank to external links on your site', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-hammer"></i> <?php _e('Additional feature', 'horse-tools') ?></h3>
	<!-- other 1 -->
	<?php horsetools_toggle( 'post-other1', __( 'Allow using Shortcode in post titles', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'Additional feature',
		'description' => __( 'This feature allows you to add Shortcode to post titles, which is very convenient for using custom tools', 'horse-tools' ),
	) ); ?>
	<!-- other 2 -->
	<?php horsetools_toggle( 'post-other2', __( 'Newly edited posts will be displayed first', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'Additional feature',
		'description' => __( 'This feature allows you to set newly edited posts to be displayed first in the main loop', 'horse-tools' ),
	) ); ?>
  <h3><i class="ti ti-eye-off"></i> <?php _e('Hide post categories from homepage', 'horse-tools') ?></h3>
	<!-- an chuyen muc khoi index -->
	<?php horsetools_toggle( 'post-hiden1', __( 'Enable if you want to hide categories', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Hide post categories from homepage',
	) ); ?>
	<p>
	<input class="ht-input-big" placeholder="1, 2, 3, 4, 5" name="horsetools_settings[post-hiden11]" type="text" value="<?php if(!empty($horsetools_options['post-hiden11'])){echo sanitize_text_field($horsetools_options['post-hiden11']);} ?>"/>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enable and add the category IDs you want to hide from the main loop displaying posts on the homepage, for example: 1, 2, 3', 'horse-tools'); ?></p>
  <h3><i class="ti ti-photo"></i> <?php _e('Image lightbox', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'post-fancy1', __( 'Enable image lightbox', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'Image lightbox',
		'description' => __( 'Click an image in your content to open it in a full-screen viewer. Choose the engine and how it looks below.', 'horse-tools' ),
	) ); ?>

	<p class="ht-field" data-ht-parent="ht-main-post-fancy1">
	<label class="ht-field-label"><?php _e('Lightbox engine', 'horse-tools'); ?></label>
	<?php
	$ht_fb_engine = ( ! empty( $horsetools_options['post-fancy-engine'] ) && 'photoswipe' === $horsetools_options['post-fancy-engine'] ) ? 'photoswipe' : 'glightbox';
	$ht_fb_engine_opts = array(
		'glightbox'  => __( 'GLightbox — light, images + video, slide effects', 'horse-tools' ),
		'photoswipe' => __( 'PhotoSwipe — best pinch-zoom / pan for photos', 'horse-tools' ),
	);
	echo '<select name="horsetools_settings[post-fancy-engine]">';
	foreach ( $ht_fb_engine_opts as $v => $l ) { echo '<option value="' . esc_attr( $v ) . '"' . selected( $ht_fb_engine, $v, false ) . '>' . esc_html( $l ) . '</option>'; }
	echo '</select>';
	?>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Both engines are free and open-source (MIT). GLightbox is the easy all-rounder (adds video + slide transitions); PhotoSwipe has the smoothest zoom/pan for photo galleries.', 'horse-tools'); ?></p>

	<p class="ht-field" data-ht-parent="ht-main-post-fancy1">
	<label class="ht-field-label"><?php _e('Where to run it', 'horse-tools'); ?></label>
	<?php
	$ht_fb_scope = ! empty( $horsetools_options['post-fancy-scope'] ) ? $horsetools_options['post-fancy-scope'] : 'post';
	$ht_fb_scope_opts = array(
		'post' => __( 'Posts only', 'horse-tools' ),
		'page' => __( 'Posts and pages', 'horse-tools' ),
		'all'  => __( 'All single content (incl. custom types)', 'horse-tools' ),
	);
	echo '<select name="horsetools_settings[post-fancy-scope]">';
	foreach ( $ht_fb_scope_opts as $v => $l ) { echo '<option value="' . esc_attr( $v ) . '"' . selected( $ht_fb_scope, $v, false ) . '>' . esc_html( $l ) . '</option>'; }
	echo '</select>';
	?>
	</p>

	<?php horsetools_toggle( 'post-fancy11', __( 'Group images into a gallery (previous / next)', 'horse-tools' ), array(
		'tab'         => 'CONTENT',
		'section'     => 'Image lightbox',
		'parent'      => 'post-fancy1',
		'description' => __( 'Links every image in the content together so the viewer can flip through them. If an image already links to a file, that link is used.', 'horse-tools' ),
	) ); ?>

	<p class="ht-field" data-ht-parent="ht-main-post-fancy1">
	<label class="ht-field-label"><?php _e('Caption from', 'horse-tools'); ?></label>
	<?php
	$ht_fb_cap = ! empty( $horsetools_options['post-fancy-caption'] ) ? $horsetools_options['post-fancy-caption'] : 'alt';
	$ht_fb_cap_opts = array(
		'alt'        => __( 'Image alt text', 'horse-tools' ),
		'figcaption' => __( 'Image caption (figcaption)', 'horse-tools' ),
		'title'      => __( 'Image title', 'horse-tools' ),
		'none'       => __( 'No caption', 'horse-tools' ),
	);
	echo '<select name="horsetools_settings[post-fancy-caption]">';
	foreach ( $ht_fb_cap_opts as $v => $l ) { echo '<option value="' . esc_attr( $v ) . '"' . selected( $ht_fb_cap, $v, false ) . '>' . esc_html( $l ) . '</option>'; }
	echo '</select>';
	?>
	</p>

	<p class="ht-field" data-ht-parent="ht-main-post-fancy1">
	<label class="ht-field-label"><?php _e('Open animation', 'horse-tools'); ?></label>
	<?php
	$ht_fb_anim = ! empty( $horsetools_options['post-fancy-anim'] ) ? $horsetools_options['post-fancy-anim'] : 'zoom';
	$ht_fb_anim_opts = array(
		'zoom' => __( 'Zoom', 'horse-tools' ),
		'fade' => __( 'Fade', 'horse-tools' ),
		'none' => __( 'None (instant)', 'horse-tools' ),
	);
	echo '<select name="horsetools_settings[post-fancy-anim]">';
	foreach ( $ht_fb_anim_opts as $v => $l ) { echo '<option value="' . esc_attr( $v ) . '"' . selected( $ht_fb_anim, $v, false ) . '>' . esc_html( $l ) . '</option>'; }
	echo '</select>';
	?>
	</p>

	<p class="ht-field" data-ht-parent="ht-main-post-fancy1">
	<label class="ht-field-label"><?php _e('Slide transition (GLightbox)', 'horse-tools'); ?></label>
	<?php
	$ht_fb_slide = ! empty( $horsetools_options['post-fancy-slide'] ) ? $horsetools_options['post-fancy-slide'] : 'slide';
	$ht_fb_slide_opts = array(
		'slide' => __( 'Slide', 'horse-tools' ),
		'fade'  => __( 'Fade', 'horse-tools' ),
		'zoom'  => __( 'Zoom', 'horse-tools' ),
		'none'  => __( 'None', 'horse-tools' ),
	);
	echo '<select name="horsetools_settings[post-fancy-slide]">';
	foreach ( $ht_fb_slide_opts as $v => $l ) { echo '<option value="' . esc_attr( $v ) . '"' . selected( $ht_fb_slide, $v, false ) . '>' . esc_html( $l ) . '</option>'; }
	echo '</select>';
	?>
	</p>

	<p class="ht-field" data-ht-parent="ht-main-post-fancy1">
	<label class="ht-field-label"><?php _e('Backdrop theme', 'horse-tools'); ?></label>
	<?php
	$ht_fb_theme = ! empty( $horsetools_options['post-fancy-theme'] ) ? $horsetools_options['post-fancy-theme'] : 'dark';
	$ht_fb_theme_opts = array(
		'dark'   => __( 'Dark', 'horse-tools' ),
		'blur'   => __( 'Blur (frosted glass)', 'horse-tools' ),
		'light'  => __( 'Light', 'horse-tools' ),
		'cinema' => __( 'Cinema (pure black)', 'horse-tools' ),
	);
	echo '<select name="horsetools_settings[post-fancy-theme]">';
	foreach ( $ht_fb_theme_opts as $v => $l ) { echo '<option value="' . esc_attr( $v ) . '"' . selected( $ht_fb_theme, $v, false ) . '>' . esc_html( $l ) . '</option>'; }
	echo '</select>';
	?>
	</p>

	<p class="ht-field" data-ht-parent="ht-main-post-fancy1">
	<label class="ht-field-label"><?php _e('Accent colour (toolbar / active thumbnail)', 'horse-tools'); ?></label>
	<input class="ht-input-color" name="horsetools_settings[post-fancy-accent]" type="text" data-coloris value="<?php if(!empty($horsetools_options['post-fancy-accent'])){echo esc_attr($horsetools_options['post-fancy-accent']);} ?>"/>
	</p>

	<?php horsetools_toggle( 'post-fancy-loop', __( 'Loop back to the first image', 'horse-tools' ), array(
		'tab'     => 'CONTENT',
		'section' => 'Image lightbox',
		'parent'  => 'post-fancy1',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Both engines are bundled locally — no external requests. Gallery grouping, looping and the slide transition apply to GLightbox; PhotoSwipe always groups the content images (previous/next) and has its own smooth zoom/pan.', 'horse-tools'); ?></p>
</div>

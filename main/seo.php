<?php
/**
 * Horse Tools — the SEO screen.
 *
 * The first screen of the reorganisation. Everything here already existed; it
 * was spread across the CONTENT tab under headings nobody looking for SEO would
 * think to open, which is how a site owner concludes the plugin has no SEO
 * features at all. Nothing about what these settings do has changed — only
 * where they are.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** The tabs on this screen, in order. */
function horsetools_seo_tabs() {
	return array(
		'seo-tab1' => array(
			'label' => __( 'Links & URLs', 'horse-tools' ),
			'icon'  => 'ti-link',
			'file'  => 'main/section/seo-url.php',
		),
		'seo-tab2' => array(
			'label' => __( 'Rich results', 'horse-tools' ),
			'icon'  => 'ti-help-circle',
			'file'  => 'main/section/seo-faq.php',
		),
	);
}

function horsetools_seo_page() {
	global $horsetools_options;
	$tabs  = horsetools_seo_tabs();
	$first = true;
	ob_start();
	?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top"></div>
	<div class="ht-wrap2">
	  <div class="ht-box">

		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://tranduythuan.com/" target="_blank">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<?php foreach ( $tabs as $id => $tab ) : ?>
			<button class="sotab<?php echo $first ? ' sotab-select' : ''; ?>" onclick="httab(event, '<?php echo esc_attr( $id ); ?>')"><i class="ti <?php echo esc_attr( $tab['icon'] ); ?>"></i> <?php echo esc_html( $tab['label'] ); ?></button>
			<?php $first = false; endforeach; ?>
		</div>

		<div class="ht-main">
			<?php if ( isset( $_GET['settings-updated'] ) ) { require( HORSETOOLS_DIR . 'main/completed.php' ); } ?>
			<form method="post" action="<?php echo esc_url( horsetools_save_url() ); ?>">
			<?php
			horsetools_save_fields();
			// Only this screen's own fields are declared, so saving here leaves
			// every other screen's settings alone — the whole point of the
			// scoped save added in 1.2.66.
			ob_start();
			$first = true;
			foreach ( $tabs as $id => $tab ) {
				printf(
					'<div class="sotab-box htbox" id="%s"%s>',
					esc_attr( $id ),
					$first ? '' : ' style="display:none"'
				);
				echo '<div class="ht-card">';
				include( HORSETOOLS_DIR . $tab['file'] );
				echo '</div></div>';
				$first = false;
			}
			horsetools_scope_print( ob_get_clean(), 'horsetools_settings' );
			?>
			<div class="ht-submit">
				<button type="submit"><i class="ti ti-device-floppy"></i> <?php _e( 'SAVE CONTENT', 'horse-tools' ); ?></button>
			</div>
				<button id="ht-save-fast" type="submit"><i class="ti ti-device-floppy"></i></button>
			</form>
		</div>

	  </div>
	  <div class="ht-sidebar">
		<?php if ( function_exists( 'horsetools_health_card' ) ) { horsetools_health_card(); } ?>
	  </div>
	</div>
	</div>
	<?php
	require_once( HORSETOOLS_DIR . 'main/style.php' );
	echo ob_get_clean();
}

function horsetools_seo_menu() {
	add_submenu_page(
		'horsetools-options',
		'SEO',
		'<i class="ti ti-chart-arrows-vertical" style="width:20px;"></i> ' . __( 'SEO', 'horse-tools' ),
		'manage_options',
		'horsetools-seo-options',
		'horsetools_seo_page'
	);
}
add_action( 'admin_menu', 'horsetools_seo_menu', 20 );

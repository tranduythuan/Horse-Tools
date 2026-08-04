<?php
/**
 * Horse Tools — the shell every grouped settings screen shares.
 *
 * The screens are being regrouped by subject, and each one is the same page:
 * a tab strip, a form, the sections, one Save button. Written out per screen
 * that would be nine near-identical copies, and the ninth would already have
 * drifted from the first — which is how the settings ended up needing this
 * reorganisation in the first place. So the shell lives here and a screen is
 * reduced to what actually differs: its slug, its title, and its tabs.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Render a grouped settings screen.
 *
 * @param array $tabs  id => array( label, icon, files[] ). Each file is a
 *                     section relative to the plugin directory.
 */
function horsetools_group_render( array $tabs ) {
	global $horsetools_options;
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
			<?php $first = true; foreach ( $tabs as $id => $tab ) : ?>
			<button class="sotab<?php echo $first ? ' sotab-select' : ''; ?>" onclick="httab(event, '<?php echo esc_attr( $id ); ?>')"><i class="ti <?php echo esc_attr( $tab['icon'] ); ?>"></i> <?php echo esc_html( $tab['label'] ); ?></button>
			<?php $first = false; endforeach; ?>
		</div>

		<div class="ht-main">
			<?php if ( isset( $_GET['settings-updated'] ) ) { require( HORSETOOLS_DIR . 'main/completed.php' ); } ?>
			<form method="post" action="<?php echo esc_url( horsetools_save_url() ); ?>">
			<?php
			horsetools_save_fields();
			// Buffer the sections so the form can declare exactly which option
			// keys this screen writes. Without that, saving here would rewrite
			// the whole shared option and wipe every other screen.
			ob_start();
			$first = true;
			foreach ( $tabs as $id => $tab ) {
				printf(
					'<div class="sotab-box htbox" id="%s"%s><div class="ht-card">',
					esc_attr( $id ),
					$first ? '' : ' style="display:none"'
				);
				foreach ( (array) $tab['files'] as $file ) {
					include( HORSETOOLS_DIR . $file );
				}
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

/**
 * Add a grouped screen to the menu.
 *
 * Position controls the order of the whole Horse Tools menu, so the groups can
 * be listed by subject rather than by whichever file happened to register
 * first. add_submenu_page() takes it as the array index of the submenu.
 *
 * @param string $slug     Page slug, without the horsetools- prefix.
 * @param string $title    Menu label.
 * @param string $icon     Tabler icon class.
 * @param string $callback Render callback.
 * @param int    $position Menu position.
 */
function horsetools_group_menu( $slug, $title, $icon, $callback, $position ) {
	add_submenu_page(
		'horsetools-options',
		$title,
		'<i class="ti ' . esc_attr( $icon ) . '" style="width:20px;"></i> ' . esc_html( $title ),
		'manage_options',
		'horsetools-' . $slug,
		$callback,
		$position
	);
}

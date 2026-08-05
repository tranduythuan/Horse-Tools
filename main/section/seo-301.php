<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_redirects_options; ?>
			<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'When a URL changes or an old link should point somewhere new, this sends visitors (and Google) straight to the right page instead of a “not found” error — so you don’t lose traffic.', 'horse-tools' ); ?></span></div>
			<div class="ht-card">
			  <h3><i class="ti ti-compass"></i> <?php _e('Redirect 301 whole page', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi11', __( 'Enable site-wide 301 redirects', 'horse-tools' ), array(
					'module'  => 'redirect',
					'tab'     => '301',
					'section' => 'Redirect 301 whole page',
				) ); ?>
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[redi12]" value="<?php if(!empty($horsetools_redirects_options['redi12'])){echo sanitize_text_field($horsetools_redirects_options['redi12']);} ?>" />
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This function will redirect all of your website pages to the destination page of your choice', 'horse-tools'); ?></p>
			  <h3><i class="ti ti-compass"></i> <?php _e('Redirect 301 to a custom page', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi1', __( 'Enable 301 redirection', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '301',
					'section'     => 'Redirect 301 to a custom page',
					'description' => __( 'This function allows you to redirect 301 links to the target page', 'horse-tools' ),
				) ); ?>

				<div id="sortable-list">
				<div data-id="1" class="ui-state-default ht-button-grid">
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan11]" value="<?php if(!empty($horsetools_redirects_options['rechan11'])){echo sanitize_text_field($horsetools_redirects_options['rechan11']);} ?>" />
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan21]" value="<?php if(!empty($horsetools_redirects_options['rechan21'])){echo sanitize_text_field($horsetools_redirects_options['rechan21']);} ?>" />
				</div>
				<?php
				if (is_array($horsetools_redirects_options) || is_object($horsetools_redirects_options)) {
					foreach ($horsetools_redirects_options as $key => $value) {
						if (preg_match('/^rechan1(\d+)$/', $key, $matches) && $matches[1] != 1) {
							$n = $matches[1];
							echo '<div data-id="' . $n . '" class="ui-state-default ht-button-grid">';
							echo '<input class="ht-input-big" placeholder="'. __('Enter the link', 'horse-tools') .'" type="text" name="horsetools_redirects_settings[rechan1' . $n . ']" value="' . sanitize_text_field($horsetools_redirects_options['rechan1' . $n]) . '" />';
							echo '<input class="ht-input-big" placeholder="'. __('Enter the link', 'horse-tools') .'" type="text" name="horsetools_redirects_settings[rechan2' . $n . ']" value="' . sanitize_text_field($horsetools_redirects_options['rechan2' . $n]) . '" />';
							echo '<span id="ht-chatx">&#x2715</span>';
							echo '</div>';
						}
					}
				}
				?>
				</div>
				<span id="ht-chatmore"><i class="ti ti-plus"></i> <?php _e('Add link', 'horse-tools'); ?></span>
			</div>
			<div class="ht-card">
			  <h3><i class="ti ti-wand"></i> <?php _e('Automatic redirects', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi-autoslug', __( 'Create a 301 automatically when a post permalink changes', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '301',
					'section'     => 'Automatic redirects',
					'description' => __( 'When you change a published post or page URL — its slug, its parent, or the whole path — the old address is redirected to the new one. WordPress already does this for a simple slug change; this also covers moves that core misses, and only ever acts on a URL that would otherwise 404.', 'horse-tools' ),
				) ); ?>
				<?php
				$autoslug = function_exists( 'horsetools_autoslug_list' ) ? horsetools_autoslug_list() : array();
				if ( ! empty( $autoslug ) ) :
				?>
				<table class="ht-404-table" data-autoslug-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_autoslug_action' ) ); ?>">
					<thead><tr>
						<th><?php _e( 'From', 'horse-tools' ); ?></th>
						<th><?php _e( 'To', 'horse-tools' ); ?></th>
						<th><?php _e( 'Actions', 'horse-tools' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $autoslug as $key => $e ) : ?>
						<tr data-key="<?php echo esc_attr( $key ); ?>">
							<td class="ht-404-url"><?php echo esc_html( $e['from'] ); ?></td>
							<td class="ht-404-url"><?php echo esc_html( $e['to'] ); ?></td>
							<td class="ht-404-actions"><a href="javascript:void(0)" class="ht-autoslug-delete">&times;</a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p><a href="javascript:void(0)" class="ht-autoslug-clear"><?php _e( 'Clear all automatic redirects', 'horse-tools' ); ?></a></p>
				<?php elseif ( isset( $horsetools_redirects_options['redi-autoslug'] ) ) : ?>
					<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'No automatic redirects yet. Change a published URL and one will appear here.', 'horse-tools' ); ?></p>
				<?php endif; ?>
			</div>

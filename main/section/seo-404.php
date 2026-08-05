<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_redirects_options; ?>
			<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Handle dead links: send visitors who hit a “404 – not found” page to a page you choose, and keep a log of the broken URLs so you know what to fix.', 'horse-tools' ); ?></span></div>
			<div class="ht-card">
			  <h3><i class="ti ti-ban"></i> <?php _e('404 redirects', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi2', __( 'Enable 404 redirection', 'horse-tools' ), array(
					'module'  => 'redirect',
					'tab'     => '404',
					'section' => '404 redirects',
				) ); ?>
				<select id="horsetools-toc-page-select">
					<option value=""><?php _e('Select redirect page', 'horse-tools'); ?></option>
					<?php
					$pages = get_pages();
					foreach ($pages as $page) {
						echo '<option value="' . esc_attr($page->post_name) . '">' . esc_html($page->post_title) . '</option>';
					}
					?>
				</select>
				<div id="horsetools-toc-tags">
					<?php 
					if (!empty($horsetools_redirects_options['redi21'])) {
						$page_slug = $horsetools_redirects_options['redi21'];
						if (!empty($page_slug)) {
							echo '<span class="horsetools-toc-tag">' . esc_html($page_slug) . ' <span class="remove-tag" data-slug="' . esc_attr($page_slug) . '">&times;</span></span>';
						}
					} 
					?>
				</div>
				<input id="horsetools-hi-input" class="ht-input-big" type="text" style="display:none;" name="horsetools_redirects_settings[redi21]" value="<?php if(!empty($horsetools_redirects_options['redi21'])){echo sanitize_text_field($horsetools_redirects_options['redi21']);} ?>" />
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Redirect the 404 page to the homepage or a custom page of your choice, leave the field blank if you want to redirect to the homepage', 'horse-tools'); ?></p>
			</div>
			<div class="ht-card">
			  <h3><i class="ti ti-clipboard-list"></i> <?php _e('404 log', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi-404log', __( 'Record 404 hits', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '404',
					'section'     => '404 log',
					'description' => __( 'Log the dead URLs anonymous visitors actually hit, so you can turn the busy ones into redirects. Logged-in users, bots and asset requests are not recorded, and nothing leaves your site.', 'horse-tools' ),
				) ); ?>
				<?php
				$log_rows = function_exists( 'horsetools_404_recent' ) ? horsetools_404_recent( 100 ) : array();
				if ( ! empty( $log_rows ) ) :
				?>
				<table class="ht-404-table" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_404_action' ) ); ?>">
					<thead><tr>
						<th><?php _e( 'Requested URL', 'horse-tools' ); ?></th>
						<th><?php _e( 'Hits', 'horse-tools' ); ?></th>
						<th><?php _e( 'Last seen', 'horse-tools' ); ?></th>
						<th><?php _e( 'Actions', 'horse-tools' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $log_rows as $row ) : ?>
						<tr data-id="<?php echo (int) $row->id; ?>" data-url="<?php echo esc_attr( $row->url ); ?>">
							<td class="ht-404-url"><?php echo esc_html( $row->url ); ?></td>
							<td><?php echo (int) $row->hits; ?></td>
							<td><?php echo esc_html( wp_date( 'Y-m-d H:i', strtotime( $row->last_seen ) ) ); ?></td>
							<td class="ht-404-actions">
								<a href="javascript:void(0)" class="ht-404-redirect" title="<?php esc_attr_e( 'Create a 301 redirect from this URL', 'horse-tools' ); ?>"><i class="ti ti-compass"></i> <?php _e( 'Redirect', 'horse-tools' ); ?></a>
								<a href="javascript:void(0)" class="ht-404-ignore" title="<?php esc_attr_e( 'Hide this URL from the log', 'horse-tools' ); ?>"><?php _e( 'Ignore', 'horse-tools' ); ?></a>
								<a href="javascript:void(0)" class="ht-404-delete" title="<?php esc_attr_e( 'Delete this log entry', 'horse-tools' ); ?>">&times;</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p><a href="javascript:void(0)" class="ht-404-clear"><?php _e( 'Clear the whole log', 'horse-tools' ); ?></a></p>
				<?php elseif ( horsetools_404_logging_on() ) : ?>
					<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'No 404s recorded yet.', 'horse-tools' ); ?></p>
				<?php endif; ?>
			</div>

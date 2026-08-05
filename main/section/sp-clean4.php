<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_clean_options;
// $targets and $next_run were set in the body of the screen this section was
// lifted out of. Without them the schedule loop runs over nothing and the tab
// renders as an empty panel — no error, just a feature that looks missing.
$targets  = function_exists( 'horsetools_clean_targets' ) ? horsetools_clean_targets() : array();
$next_run = wp_next_scheduled( 'horsetools_scheduled_clean' );
	$freq_opts  = array(
		'off'     => __( 'Off', 'horse-tools' ),
		'daily'   => __( 'Daily', 'horse-tools' ),
		'weekly'  => __( 'Weekly', 'horse-tools' ),
		'monthly' => __( 'Monthly', 'horse-tools' ),
	);
?>
			<div class="ht-card">
			   <h3><i class="ti ti-history"></i> <?php _e( 'Automatic cleanup', 'horse-tools' ); ?></h3>
				<?php
				foreach ( $targets as $id => $target ) {
					if ( empty( $target['schedulable'] ) ) {
						continue;
					}
					$field   = 'cron-' . $id;
					$current = horsetools_opt( 'clean', $field, 'off' );
					$field_id = 'ht-clean-' . sanitize_html_class( $id );
					?>
					<p class="ht-field">
						<label class="ht-field-label" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $target['label'] ); ?></label>
						<select id="<?php echo esc_attr( $field_id ); ?>" name="horsetools_clean_settings[<?php echo esc_attr( $field ); ?>]">
							<?php foreach ( $freq_opts as $value => $text ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $text ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				}
				?>
				<p class="ht-note"><i class="ti ti-bulb"></i>
					<?php _e( 'Cleanup runs on WordPress cron, which only fires when your site receives traffic. Weekly and monthly are measured from the last run.', 'horse-tools' ); ?>
					<?php if ( $next_run ) : ?>
						<br><?php printf( esc_html__( 'Next automatic check: %s', 'horse-tools' ), esc_html( wp_date( 'Y-m-d H:i', $next_run ) ) ); ?>
					<?php endif; ?>
				</p>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Deleting comments by link pattern is intentionally excluded from automatic cleanup — it stays a manual action.', 'horse-tools' ); ?></p>
			</div>

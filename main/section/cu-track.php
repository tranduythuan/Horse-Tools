<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
$ht_track = function_exists( 'horsetools_track_detect' ) ? horsetools_track_detect() : array( 'found' => false, 'id' => '', 'how' => '' );
?>
			<h3><i class="ti ti-chart-dots"></i> <?php _e( 'Contact click measurement', 'horse-tools' ) ?></h3>

			<?php if ( $ht_track['found'] ) : ?>
			<p class="ht-note"><i class="ti ti-circle-check"></i>
				<?php
				printf(
					/* translators: %s: the measurement ID found on the site, e.g. G-XXXXXXX. */
					esc_html__( 'Analytics found on your site: %s. Clicks will be recorded there.', 'horse-tools' ),
					'<code>' . esc_html( $ht_track['id'] ) . '</code>'
				);
				?>
			</p>
			<?php else : ?>
			<p class="ht-note ht-note-red"><i class="ti ti-alert-triangle"></i>
				<?php esc_html_e( 'No Google Analytics tag was found on your site, so there is nowhere for clicks to be recorded yet. Install Site Kit by Google (or any GA4 plugin) first — this setting will then start working on its own.', 'horse-tools' ); ?>
			</p>
			<?php endif; ?>

			<p>
				<a class="ht-btn-sub" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'horsetools-track-recheck', '1' ), 'horsetools_track_recheck' ) ); ?>"><i class="ti ti-refresh"></i> <?php esc_html_e( 'Check again', 'horse-tools' ); ?></a>
			</p>

			<?php
			horsetools_toggle( 'track-contact1', __( 'Record contact button clicks', 'horse-tools' ), array(
				'tab'         => 'CUSTOMERS',
				'section'     => 'Contact click measurement',
				'description' => __( 'Sends one event to your analytics each time a visitor taps a phone, Zalo, Messenger, Telegram, WhatsApp, Viber, SMS or email link — including links inside your posts, not only the chat buttons. Nothing is stored on your site and no personal data is involved.', 'horse-tools' ),
			) );
			?>

			<h4><?php _e( 'Where to read the numbers', 'horse-tools' ) ?></h4>
			<p class="ht-note"><i class="ti ti-bulb"></i>
				<?php esc_html_e( 'In Google Analytics: Reports → Engagement → Events. Each channel appears under its own name — contact_phone, contact_zalo, contact_messenger and so on — so there is nothing to configure first. Allow 24 hours for the standard reports; to see it immediately instead, open Admin → DebugView and tap a button on your phone.', 'horse-tools' ); ?>
			</p>
			<p class="ht-note"><i class="ti ti-bulb"></i>
				<?php esc_html_e( 'Optional: in Admin → Events, switch on "Mark as key event" for the channels that matter to you. They then appear in the acquisition reports, so you can see which traffic source produces contacts, and they can be imported into Google Ads as a conversion.', 'horse-tools' ); ?>
			</p>
			<p class="ht-note"><i class="ti ti-alert-triangle"></i>
				<?php esc_html_e( 'Read these as intent, not outcome: a tap means someone opened the dialler or the chat app, not that a call connected or a message was sent. Ad blockers also stop some clicks reaching Analytics, so the real number is a little higher than the one you see.', 'horse-tools' ); ?>
			</p>

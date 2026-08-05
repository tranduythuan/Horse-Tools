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
			<?php elseif ( 'unreachable' === $ht_track['how'] ) : ?>
			<p class="ht-note"><i class="ti ti-help-circle"></i>
				<?php esc_html_e( 'Could not check: this server was unable to open your own home page, which many hosts block. That says nothing about whether analytics is installed — if you have Site Kit or another GA4 plugin, switch this on and check the result in Analytics under Admin → DebugView.', 'horse-tools' ); ?>
			</p>
			<?php else : ?>
			<p class="ht-note"><i class="ti ti-help-circle"></i>
				<?php esc_html_e( 'No Google Analytics tag turned up, either in your settings or on the home page. If you have not installed one yet, Site Kit by Google (or any GA4 plugin) is the usual way. If you know you do have one — added through Tag Manager, a caching layer, or a consent tool that loads it later — this check simply could not see it: switch the setting on anyway. It looks for analytics at the moment of the click, not now, so it starts working the instant a tag is present.', 'horse-tools' ); ?>
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
			<?php if ( 'GTM' === substr( (string) $ht_track['id'], 0, 3 ) ) : ?>
			<h4><?php _e( 'You are using Tag Manager — one setup step is required', 'horse-tools' ) ?></h4>
			<p class="ht-note ht-note-red"><i class="ti ti-alert-triangle"></i>
				<?php esc_html_e( 'Tag Manager does not pass events on by itself. The click is placed in the dataLayer and stops there until you build a tag for it, so nothing reaches Analytics until this is done once.', 'horse-tools' ); ?>
			</p>
			<p class="ht-note"><i class="ti ti-bulb"></i>
				<?php esc_html_e( '1. Triggers → New → Custom Event, event name ^contact_ with "use regex matching" ticked. 2. Tags → New → Google Analytics GA4 Event, pointing at your measurement ID, Event Name set to {{Event}} so each channel keeps its own name, using the trigger from step 1. 3. Submit and publish the container.', 'horse-tools' ); ?>
			</p>
			<?php endif; ?>

			<p class="ht-note"><i class="ti ti-alert-triangle"></i>
				<?php esc_html_e( 'Read these as intent, not outcome: a tap means someone opened the dialler or the chat app, not that a call connected or a message was sent. Ad blockers also stop some clicks reaching Analytics, so the real number is a little higher than the one you see.', 'horse-tools' ); ?>
			</p>

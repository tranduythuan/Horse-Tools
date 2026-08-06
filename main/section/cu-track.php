<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
$ht_track = function_exists( 'horsetools_track_detect' ) ? horsetools_track_detect() : array( 'found' => false, 'id' => '', 'how' => '' );
?>
			<h3><i class="ti ti-chart-dots"></i> <?php _e( 'Contact click measurement', 'horse-tools' ) ?></h3>

			<div id="ht-track-state">
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
			</div>

			<p>
				<a class="ht-btn-sub" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'horsetools-track-recheck', '1' ), 'horsetools_track_recheck' ) ); ?>"><i class="ti ti-refresh"></i> <?php esc_html_e( 'Check again', 'horse-tools' ); ?></a>
			</p>

			<?php
			/**
			 * Ask the browser, not the server.
			 *
			 * Every server-side answer here is a guess about what a visitor
			 * receives: a host can block a site from fetching itself, route the
			 * loopback to a different site on the same account, or hand it a
			 * page built for bots — and the analytics tag may not live in the
			 * options table at all if it was put in a theme file. The admin's own
			 * browser has none of those problems. The home page is same-origin,
			 * so it can simply be read; sent without cookies, because plugins
			 * routinely leave the tag out for logged-in users and the tag we care
			 * about is the one a visitor gets.
			 */
			$ht_probe = array(
				'home'  => home_url( '/' ),
				/* translators: %s: the measurement ID found on the site, e.g. G-XXXXXXX. */
				'found' => __( 'Analytics found on your site: %s. Clicks will be recorded there.', 'horse-tools' ),
				'none'  => __( 'Checked from your own browser: the home page a visitor receives carries no Google Analytics tag. Install Site Kit by Google, or any GA4 plugin, and this setting starts working on its own.', 'horse-tools' ),
			);
			?>
			<script>
			(function () {
				var cfg = <?php echo wp_json_encode( $ht_probe ); ?>;
				var box = document.getElementById('ht-track-state');
				if (!box || !window.fetch) { return; }

				fetch(cfg.home, { credentials: 'omit', cache: 'no-store' })
					.then(function (r) { return r.ok ? r.text() : ''; })
					.then(function (html) {
						if (!html) { return; }   // leave the server's answer alone
						var m = html.match(/\bG-[A-Z0-9]{6,}\b/) || html.match(/\bGTM-[A-Z0-9]{4,}\b/);
						var p = document.createElement('p');
						var i = document.createElement('i');
						p.className = 'ht-note';
						i.className = m ? 'ti ti-circle-check' : 'ti ti-alert-triangle';
						p.appendChild(i);
						p.appendChild(document.createTextNode(
							' ' + (m ? cfg.found.replace('%s', m[0]) : cfg.none)
						));
						if (!m) { p.className = 'ht-note ht-note-red'; }
						box.innerHTML = '';
						box.appendChild(p);
					})
					.catch(function () {});   // offline or blocked: the server's answer stands
			}());
			</script>

			<?php if ( 'GTM' === substr( (string) $ht_track['id'], 0, 3 ) || horsetools_track_has_gtm() ) : ?>
			<p class="ht-note ht-note-red"><i class="ti ti-alert-triangle"></i>
				<?php esc_html_e( 'Tag Manager is installed on this site. If you already built click tags there — a trigger on tel: links, on zalo.me, on m.me — switching this on records the same click twice, once under your own event name and once as contact_phone. Open your container, check whether such tags exist, and pause either those or this setting. Keep whichever one also fires your Google Ads conversions; this setting does not.', 'horse-tools' ); ?>
			</p>
			<?php endif; ?>

			<?php
			horsetools_toggle( 'track-contact1', __( 'Record contact button clicks', 'horse-tools' ), array(
				'tab'         => 'CUSTOMERS',
				'section'     => 'Contact click measurement',
				'description' => __( 'Sends one event to your analytics each time a visitor taps a contact link — phone, SMS, email, Zalo, Messenger, Telegram, WhatsApp, Viber, Line, WeChat, TikTok, Maps and the rest. Any other chat button is recorded under its own site name, so Instagram, Shopee, Signal and anything you add later are covered without waiting for an update. Phone, SMS, email and Viber links count anywhere on the site, including inside your posts; the others count inside the chat widgets, so an article full of outbound links does not flood the report. Nothing is stored on your site and no personal data is involved.', 'horse-tools' ),
			) );
			?>

			<h4><?php _e( 'Where to read the numbers', 'horse-tools' ) ?></h4>
			<p class="ht-note"><i class="ti ti-bulb"></i>
				<?php esc_html_e( 'In Google Analytics: Reports → Engagement → Events. Each channel appears under its own name — contact_phone, contact_zalo, contact_messenger and so on — so there is nothing to configure first. Allow 24 hours for the standard reports; to see it immediately instead, open Admin → DebugView and tap a button on your phone.', 'horse-tools' ); ?>
			</p>
			<p class="ht-note"><i class="ti ti-bulb"></i>
				<?php
				printf(
					/* translators: %s: the ?ht_debug=1 query string, wrapped in <code>. */
					esc_html__( 'To check it works right now rather than waiting: open your site with %s on the end of the address, tap a contact button, and watch GA4 → Admin → DebugView. Without that flag DebugView stays empty however many times you tap, because it only lists devices that identify themselves as debug devices — which is not a fault in the buttons.', 'horse-tools' ),
					'<code>?ht_debug=1</code>'
				);
				?>
			</p>
			<p class="ht-note"><i class="ti ti-bulb"></i>
				<?php esc_html_e( 'Optional: in Admin → Events, switch on "Mark as key event" for the channels that matter to you. They then appear in the acquisition reports, so you can see which traffic source produces contacts, and they can be imported into Google Ads as a conversion.', 'horse-tools' ); ?>
			</p>
			<?php
			/**
			 * Only the case this site is actually in.
			 *
			 * These instructions used to appear for anyone with a container,
			 * which asks the wrong question: a GA4 tag inside a container brings
			 * gtag() with it, and those owners have nothing to do. Sending them
			 * to build a trigger is a wasted afternoon; hiding it from the owners
			 * who do need it is a silent failure. horsetools_track_route() opens
			 * the container and settles it.
			 */
			$ht_route = isset( $ht_track['route'] ) ? $ht_track['route'] : 'unknown';
			?>
			<div id="ht-track-route">
			<?php if ( 'gtag' === $ht_route ) : ?>
			<h4><?php _e( 'Nothing to set up', 'horse-tools' ) ?></h4>
			<p class="ht-note"><i class="ti ti-circle-check"></i>
				<?php esc_html_e( 'Your site loads the Google tag, so a click goes straight to Analytics. This is the usual case, and it includes sites where Analytics was installed through Tag Manager — a GA4 tag inside a container loads the Google tag itself.', 'horse-tools' ); ?>
			</p>
			<?php elseif ( 'none' === $ht_route ) : ?>
			<h4><?php _e( 'Install analytics first', 'horse-tools' ) ?></h4>
			<p class="ht-note"><i class="ti ti-info-circle"></i>
				<?php esc_html_e( 'The page carries no analytics at all yet, so there is nowhere for a click to be recorded. Install Site Kit by Google, or any GA4 plugin, and this setting starts working on its own with nothing further to configure.', 'horse-tools' ); ?>
			</p>
			<?php else : ?>
			<?php if ( 'datalayer' === $ht_route ) : ?>
			<h4><?php _e( 'Your site has Tag Manager but no Google tag — one setup step is required', 'horse-tools' ) ?></h4>
			<p class="ht-note ht-note-red"><i class="ti ti-alert-triangle"></i>
				<?php esc_html_e( 'Your container has no GA4 tag in it, so Tag Manager has nothing to pass the click on to. It is placed in the dataLayer and stops there until you build the tag below — until then nothing reaches Analytics.', 'horse-tools' ); ?>
			</p>
			<?php else : ?>
			<h4><?php _e( 'If your site has Tag Manager but no Google tag', 'horse-tools' ) ?></h4>
			<p class="ht-note"><i class="ti ti-help-circle"></i>
				<?php esc_html_e( 'This could not be checked from here, so the instructions are shown just in case. To find out whether they apply to you: open your site, press F12, and type gtag in the console. An answer of "function" means you have nothing to do; "undefined" means follow the steps below.', 'horse-tools' ); ?>
			</p>
			<?php endif; ?>
			<p class="ht-note"><i class="ti ti-bulb"></i>
				<?php esc_html_e( '1. Triggers → New → Custom Event, event name ^contact_ with "use regex matching" ticked — one trigger covers every channel, including any added later. 2. Tags → New → Google Analytics GA4 Event, pointing at your measurement ID, Event Name set to {{Event}} so each channel keeps its own name, using the trigger from step 1. 3. Optionally add event parameters placement and label, taking their values from Data Layer Variables contact_placement and contact_label. 4. Submit and publish the container.', 'horse-tools' ); ?>
			</p>
			<?php endif; ?>
			</div>

			<p class="ht-note"><i class="ti ti-alert-triangle"></i>
				<?php esc_html_e( 'Read these as intent, not outcome: a tap means someone opened the dialler or the chat app, not that a call connected or a message was sent. Ad blockers also stop some clicks reaching Analytics, so the real number is a little higher than the one you see.', 'horse-tools' ); ?>
			</p>

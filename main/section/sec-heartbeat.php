<?php
/**
 * Horse Tools — the check-in message settings.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;

$hb_state   = function_exists( 'horsetools_hb_state' ) ? horsetools_hb_state() : array( 'seq' => 0, 'ever' => false, 'ok' => false, 'error' => '', 'sent' => 0, 'due' => 0 );
$hb_channel = function_exists( 'horsetools_alert_channel' ) ? horsetools_alert_channel() : 'email';
?>
<h3><i class="ti ti-heartbeat"></i> <?php esc_html_e( 'Tell me the site is still being watched', 'horse-tools' ); ?></h3>

<p class="ht-note" style="max-width:52em">
	<i class="ti ti-bulb"></i>
	<?php esc_html_e( 'Everything else on this page warns you when something is wrong — but only on a screen you have to open. It cannot warn you that it stopped: a site with the plugin switched off looks exactly like a site with nothing wrong.', 'horse-tools' ); ?>
	<br>
	<?php esc_html_e( 'So this sends you a short message on a schedule, with a number on it, even when everything is fine. Numbers that skip mean messages were sent and never reached you. A message that arrives late means nothing was running the schedule. Nothing at all means something stopped it — and that is the one you can only notice from outside the site, which is why each message tells you when to expect the next.', 'horse-tools' ); ?>
</p>

<?php
horsetools_toggle(
	'watch-hb',
	__( 'Send me a regular check-in message', 'horse-tools' ),
	array(
		'tab'         => 'SECURITY',
		'section'     => 'Tell me the site is still being watched',
		'description' => __( 'The first one goes out as soon as you save, so you find out straight away whether the channel works.', 'horse-tools' ),
	)
);
?>

<p class="ht-field" data-ht-parent="<?php echo esc_attr( horsetools_field_id( 'main', 'watch-hb' ) ); ?>">
	<label for="ht-watch-hb-days" class="ht-label-left"><?php esc_html_e( 'How often', 'horse-tools' ); ?></label>
	<select id="ht-watch-hb-days" name="horsetools_settings[watch-hb-days]">
		<?php
		$choices = array(
			1  => __( 'Every day', 'horse-tools' ),
			7  => __( 'Every week (recommended)', 'horse-tools' ),
			14 => __( 'Every two weeks', 'horse-tools' ),
			30 => __( 'Every month', 'horse-tools' ),
		);
		$current = isset( $horsetools_options['watch-hb-days'] ) ? (int) $horsetools_options['watch-hb-days'] : 7;
		foreach ( $choices as $days => $label ) {
			printf(
				'<option value="%d" %s>%s</option>',
				(int) $days,
				selected( $current, $days, false ),
				esc_html( $label )
			);
		}
		?>
	</select>
</p>
<p class="ht-note" data-ht-parent="<?php echo esc_attr( horsetools_field_id( 'main', 'watch-hb' ) ); ?>">
	<i class="ti ti-bulb"></i>
	<?php esc_html_e( 'Weekly is the useful setting. Daily turns the one message that is supposed to mean something into noise within a fortnight, and once it is noise a gap in the numbering is not noticed either.', 'horse-tools' ); ?>
</p>

<p class="ht-field" data-ht-parent="<?php echo esc_attr( horsetools_field_id( 'main', 'watch-hb' ) ); ?>">
	<input class="ht-input-big" id="ht-watch-tg" type="text"
		name="horsetools_settings[watch-tg]"
		placeholder="<?php esc_attr_e( 'Telegram chat ID for security messages (optional)', 'horse-tools' ); ?>"
		value="<?php echo isset( $horsetools_options['watch-tg'] ) ? esc_attr( $horsetools_options['watch-tg'] ) : ''; ?>" />
</p>
<p class="ht-note" data-ht-parent="<?php echo esc_attr( horsetools_field_id( 'main', 'watch-hb' ) ); ?>">
	<i class="ti ti-bulb"></i>
	<?php esc_html_e( 'Leave this empty and the message goes to the same Telegram chat as your order notifications, or by email if you have not set a bot up. Orders go to whoever packs them; this does not, so there is a field of its own.', 'horse-tools' ); ?>
	<br>
	<strong><?php
	/* translators: %s: where messages currently go. */
	printf( esc_html__( 'Right now these would go to: %s', 'horse-tools' ), esc_html( horsetools_alert_target() ) );
	?></strong>
	<?php if ( 'email' === $hb_channel ) : ?>
		<br>
		<?php esc_html_e( 'Email is the fallback and not a good one — a security message sent by a shop\'s own server is exactly the kind that lands in spam. Set up the Telegram bot under WooCommerce if you can.', 'horse-tools' ); ?>
	<?php endif; ?>
</p>

<p class="ht-field" data-ht-parent="<?php echo esc_attr( horsetools_field_id( 'main', 'watch-hb' ) ); ?>">
	<button type="button" class="button" id="ht-hb-test" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_hb' ) ); ?>">
		<?php esc_html_e( 'Send a test message now', 'horse-tools' ); ?>
	</button>
	<span id="ht-hb-test-out" style="margin-left:10px"></span>
</p>

<?php if ( $hb_state['ever'] ) : ?>
	<p class="ht-note">
		<i class="ti ti-history"></i>
		<?php
		printf(
			/* translators: 1: sequence number, 2: human-readable time ago. */
			esc_html__( 'Last message: #%1$d, %2$s ago.', 'horse-tools' ),
			(int) $hb_state['seq'],
			esc_html( human_time_diff( $hb_state['sent'] ) )
		);
		?>
		<?php if ( $hb_state['ok'] ) : ?>
			<?php
			printf(
				/* translators: %s: date. */
				' ' . esc_html__( 'Next one due %s.', 'horse-tools' ),
				esc_html( date_i18n( get_option( 'date_format' ), $hb_state['due'] ) )
			);
			?>
			<br><?php esc_html_e( 'The site handed it over successfully. That is not the same as it arriving — if the numbers you receive skip, the channel is dropping them.', 'horse-tools' ); ?>
		<?php else : ?>
			<br><strong style="color:#c0392b"><?php
			/* translators: %s: the error reported by the channel. */
			printf( esc_html__( 'It failed: %s', 'horse-tools' ), esc_html( $hb_state['error'] ) );
			?></strong>
		<?php endif; ?>
	</p>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var b = document.getElementById('ht-hb-test');
	var out = document.getElementById('ht-hb-test-out');
	if (!b || !out) { return; }
	b.addEventListener('click', function () {
		b.disabled = true;
		out.textContent = <?php echo wp_json_encode( __( 'Sending…', 'horse-tools' ) ); ?>;
		out.style.color = '';
		fetch(ajaxurl, {method:'POST', credentials:'same-origin',
			headers:{'Content-Type':'application/x-www-form-urlencoded'},
			body:'action=horsetools_hb_test&nonce=' + encodeURIComponent(b.dataset.nonce)})
		.then(function (r) { return r.json(); })
		.then(function (j) {
			out.textContent = (j && j.data && j.data.message) ? j.data.message : '';
			out.style.color = (j && j.success) ? '#2e9e5b' : '#c0392b';
			b.disabled = false;
		})
		.catch(function () {
			out.textContent = <?php echo wp_json_encode( __( 'The request itself failed.', 'horse-tools' ) ); ?>;
			out.style.color = '#c0392b';
			b.disabled = false;
		});
	});
});
</script>

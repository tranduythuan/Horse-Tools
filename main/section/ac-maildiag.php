<?php
/**
 * Horse Tools — what DNS says about this site's mail, and whether it arrives.
 *
 * Sits above the SMTP form deliberately. The form asks eight questions; this
 * answers the one the owner actually has, which is "is my site's email working
 * at all", and answers it before anything is filled in.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$ht_findings = horsetools_mail_findings();
$ht_proof    = horsetools_mail_proof();
$ht_row      = horsetools_mail_proof_row();
$ht_guess    = horsetools_mail_guess_provider();
$ht_nonce    = wp_create_nonce( 'horsetools_mail' );
$ht_tone     = array( 'bad' => '#c0392b', 'warn' => '#b8860b', 'note' => '#555', 'ok' => '#2e9e5b' );
$ht_icon     = array( 'bad' => 'ti-circle-x', 'warn' => 'ti-alert-triangle', 'note' => 'ti-info-circle', 'ok' => 'ti-circle-check' );
?>

<h3><i class="ti ti-mail-search"></i> <?php esc_html_e( 'Does mail from this site actually arrive?', 'horse-tools' ); ?></h3>

<p class="ht-note" style="max-width:52em">
	<i class="ti ti-bulb"></i>
	<?php esc_html_e( 'WordPress reports a message as sent once it has handed it over. That says nothing about whether it arrived, and on a great many sites it does not — the domain names which servers may send for it, the web server is not one of them, and every notification the site sends is treated as forged. Nothing in WordPress mentions this. Below is what your domain’s own records say, read live.', 'horse-tools' ); ?>
</p>

<?php if ( $ht_findings ) : ?>
	<div style="max-width:52em;border:1px solid #dcdcde;background:#fff;border-radius:4px;padding:4px 14px;margin:12px 0">
		<?php foreach ( $ht_findings as $f ) : ?>
			<p style="margin:12px 0">
				<i class="ti <?php echo esc_attr( $ht_icon[ $f['level'] ] ); ?>" style="color:<?php echo esc_attr( $ht_tone[ $f['level'] ] ); ?>"></i>
				<span style="color:<?php echo esc_attr( $ht_tone[ $f['level'] ] ); ?>"><?php echo esc_html( $f['text'] ); ?></span>
				<?php if ( '' !== $f['fix'] ) : ?>
					<br><span class="description" style="margin-left:22px"><?php echo esc_html( $f['fix'] ); ?></span>
				<?php endif; ?>
			</p>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<p>
	<button type="button" class="button" id="ht-mail-recheck" data-nonce="<?php echo esc_attr( $ht_nonce ); ?>"><?php esc_html_e( 'Read the records again', 'horse-tools' ); ?></button>
	<span class="description" style="margin-left:8px"><?php esc_html_e( 'Answers are held for half a day; press this after changing anything in DNS.', 'horse-tools' ); ?></span>
</p>

<?php if ( '' !== $ht_guess['name'] ) : ?>
	<p class="ht-note">
		<i class="ti ti-bulb"></i>
		<?php
		printf(
			/* translators: %s: mail provider name, e.g. "Google Workspace". */
			esc_html__( 'Your domain receives mail through %s. Sending through the same service is the setting most likely to work, because it is already on your domain’s list of allowed senders.', 'horse-tools' ),
			'<strong>' . esc_html( $ht_guess['name'] ) . '</strong>'
		);
		?>
	</p>
<?php endif; ?>

<h3 style="margin-top:26px"><i class="ti ti-send"></i> <?php esc_html_e( 'Prove it', 'horse-tools' ); ?></h3>

<p class="ht-note" style="max-width:52em">
	<i class="ti ti-bulb"></i>
	<?php esc_html_e( 'Nothing running on this server can find out whether a message arrived. You can. Send one, look, and say what you found — the answer is kept with the date, so this screen can afterwards report something that was seen rather than something that was switched on.', 'horse-tools' ); ?>
</p>

<p class="ht-field">
	<input class="ht-input-big" type="email" id="ht-mail-to" style="max-width:24em"
		value="<?php echo esc_attr( '' !== $ht_proof['to'] ? $ht_proof['to'] : get_option( 'admin_email' ) ); ?>"
		placeholder="<?php esc_attr_e( 'an address you can open right now', 'horse-tools' ); ?>">
	<button type="button" class="button button-primary" id="ht-mail-send" data-nonce="<?php echo esc_attr( $ht_nonce ); ?>">
		<?php esc_html_e( 'Send a test', 'horse-tools' ); ?>
	</button>
	<span id="ht-mail-out" style="margin-left:10px"></span>
</p>

<div id="ht-mail-ask" style="<?php echo 'sent' === $ht_proof['state'] ? '' : 'display:none'; ?>;max-width:52em;border-left:3px solid #3a6ea5;background:#eef3f8;padding:10px 16px;margin:10px 0">
	<p style="margin:0 0 8px"><strong><?php esc_html_e( 'Where did it turn up?', 'horse-tools' ); ?></strong></p>
	<p style="margin:0">
		<button type="button" class="button button-primary ht-mail-ans" data-state="inbox" data-nonce="<?php echo esc_attr( $ht_nonce ); ?>"><?php esc_html_e( 'In the inbox', 'horse-tools' ); ?></button>
		<button type="button" class="button ht-mail-ans" data-state="spam" data-nonce="<?php echo esc_attr( $ht_nonce ); ?>"><?php esc_html_e( 'In the spam folder', 'horse-tools' ); ?></button>
		<button type="button" class="button ht-mail-ans" data-state="missing" data-nonce="<?php echo esc_attr( $ht_nonce ); ?>"><?php esc_html_e( 'Nowhere — it never came', 'horse-tools' ); ?></button>
	</p>
	<p class="description" style="margin:8px 0 0"><?php esc_html_e( 'Give it a minute before answering. “In the spam folder” is a useful answer, not a failure to report.', 'horse-tools' ); ?></p>
</div>

<p style="margin-top:10px">
	<strong><?php esc_html_e( 'Where this stands:', 'horse-tools' ); ?></strong>
	<span style="color:<?php echo esc_attr( 'pass' === $ht_row['status'] ? '#2e9e5b' : ( 'fail' === $ht_row['status'] ? '#c0392b' : '#b8860b' ) ); ?>">
		<?php echo esc_html( $ht_row['text'] ); ?>
	</span>
	<?php if ( 'inbox' === $ht_proof['state'] && ! $ht_proof['stale'] && '' !== $ht_proof['to'] ) : ?>
		<span class="description">— <?php echo esc_html( $ht_proof['to'] ); ?></span>
	<?php endif; ?>
</p>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var post = function (body) {
		return fetch(ajaxurl, {method:'POST', credentials:'same-origin',
			headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body})
			.then(function (r) { return r.json(); });
	};

	var send = document.getElementById('ht-mail-send');
	var out  = document.getElementById('ht-mail-out');
	var ask  = document.getElementById('ht-mail-ask');
	if (send) {
		send.addEventListener('click', function () {
			var to = (document.getElementById('ht-mail-to') || {}).value || '';
			send.disabled = true;
			out.textContent = <?php echo wp_json_encode( __( 'Sending…', 'horse-tools' ) ); ?>;
			out.style.color = '';
			post('action=horsetools_mail_test&nonce=' + encodeURIComponent(send.dataset.nonce) + '&to=' + encodeURIComponent(to))
			.then(function (j) {
				out.textContent = (j && j.data && j.data.message) ? j.data.message : '';
				out.style.color = (j && j.success) ? '#2e9e5b' : '#c0392b';
				if (j && j.success && ask) { ask.style.display = ''; }
				send.disabled = false;
			})
			.catch(function () {
				out.textContent = <?php echo wp_json_encode( __( 'The request itself failed.', 'horse-tools' ) ); ?>;
				out.style.color = '#c0392b';
				send.disabled = false;
			});
		});
	}

	var answers = document.querySelectorAll('.ht-mail-ans');
	for (var i = 0; i < answers.length; i++) {
		answers[i].addEventListener('click', function (e) {
			var b = e.currentTarget;
			b.disabled = true;
			post('action=horsetools_mail_answer&nonce=' + encodeURIComponent(b.dataset.nonce) + '&state=' + encodeURIComponent(b.dataset.state))
			.then(function () { location.reload(); })
			.catch(function () { b.disabled = false; });
		});
	}

	var re = document.getElementById('ht-mail-recheck');
	if (re) {
		re.addEventListener('click', function () {
			re.disabled = true;
			post('action=horsetools_mail_recheck&nonce=' + encodeURIComponent(re.dataset.nonce))
			.then(function () { location.reload(); })
			.catch(function () { re.disabled = false; });
		});
	}
});
</script>

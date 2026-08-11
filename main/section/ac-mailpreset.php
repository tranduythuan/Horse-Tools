<?php
/**
 * Horse Tools — pick an email service and stop being asked the rest.
 *
 * Sits directly above the eight-field SMTP form and fills seven of them. The
 * form stays exactly as it was: this writes into the same inputs, so nothing new
 * is stored, nothing needs migrating, and the owner watches the values appear
 * rather than being told to trust that they did.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$ht_presets = horsetools_mail_presets();
$ht_chosen  = horsetools_mail_preset_key();
$ht_looks   = horsetools_mail_preset_detect();
$ht_guess   = function_exists( 'horsetools_mail_guess_provider' ) ? horsetools_mail_guess_provider() : array( 'key' => '', 'name' => '' );
$ht_warn    = horsetools_mail_from_warning();

// The settings search indexes what is registered, not what is on the page. A
// control this prominent being unfindable by the word "SMTP" would be a hole in
// the search rather than a missing feature here.
horsetools_register_field(
	array(
		'key'     => 'mail-preset',
		'module'  => 'main',
		'label'   => __( 'Email service (SMTP preset)', 'horse-tools' ),
		'tab'     => 'MAIL',
		'section' => 'Configure SMTP mail',
		'type'    => 'select',
	)
);
?>

<h3><i class="ti ti-plug-connected"></i> <?php esc_html_e( 'Which service should send your email?', 'horse-tools' ); ?></h3>

<p class="ht-note" style="max-width:52em">
	<i class="ti ti-bulb"></i>
	<?php esc_html_e( 'The form below asks eight questions. Seven of them have exactly one right answer once you know which service you are using, and the eighth is a password. Choose here and the seven fill themselves in — you are left with the one thing nothing can guess.', 'horse-tools' ); ?>
</p>

<?php if ( '' !== $ht_warn ) : ?>
	<div class="notice notice-error" style="max-width:52em;margin:12px 0">
		<p><strong><?php esc_html_e( 'These two addresses do not match, and that is enough to stop your mail.', 'horse-tools' ); ?></strong></p>
		<p><?php echo esc_html( $ht_warn ); ?></p>
	</div>
<?php endif; ?>

<?php
// The panel above names the provider your MX records point at. Here, where the
// list is, the useful form of that is which line of the list to pick — and only
// when the list actually has one.
$ht_sugg = horsetools_mail_preset_for_mx( $ht_guess['key'] );
?>
<?php if ( '' !== $ht_sugg && '' === $ht_chosen ) : ?>
	<p class="ht-note">
		<i class="ti ti-arrow-right"></i>
		<?php
		printf(
			/* translators: 1: provider read from the domain's MX records, 2: the entry to choose below. */
			esc_html__( 'Your domain already receives mail through %1$s, so choose %2$s below. Sending through the service that already handles your mail is the one most likely to work first time.', 'horse-tools' ),
			'<strong>' . esc_html( $ht_guess['name'] ) . '</strong>',
			'<strong>' . esc_html( horsetools_mail_preset( $ht_sugg )['label'] ) . '</strong>'
		);
		?>
	</p>
<?php endif; ?>

<p class="ht-field">
	<label for="ht-mail-preset" class="ht-label-left"><?php esc_html_e( 'Service', 'horse-tools' ); ?></label>
	<select id="ht-mail-preset" name="horsetools_settings[mail-preset]">
		<option value=""><?php esc_html_e( '— choose, or fill the form in yourself —', 'horse-tools' ); ?></option>
		<?php foreach ( $ht_presets as $key => $row ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $ht_chosen, $key ); ?>>
				<?php echo esc_html( $row['label'] ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</p>

<?php if ( '' === $ht_chosen && '' !== $ht_looks ) : ?>
	<p class="ht-note" id="ht-preset-looks">
		<i class="ti ti-info-circle"></i>
		<?php
		printf(
			/* translators: %s: service name. */
			esc_html__( 'Your settings already look like %s, so its notes apply to you even though nothing is selected above.', 'horse-tools' ),
			'<strong>' . esc_html( horsetools_mail_preset( $ht_looks )['label'] ) . '</strong>'
		);
		?>
	</p>
<?php endif; ?>

<div id="ht-preset-help" style="max-width:52em"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var PRESETS = <?php
	// Only what the browser needs to fill the form and explain itself. The table
	// itself stays in PHP, where it can be tested.
	$ht_js = array();
	foreach ( $ht_presets as $ht_k => $ht_r ) {
		$ht_js[ $ht_k ] = array(
			'label'  => $ht_r['label'],
			'host'   => $ht_r['host'],
			'port'   => $ht_r['port'],
			'enc'    => $ht_r['enc'],
			'user'   => $ht_r['user'],
			'from'   => $ht_r['from'],
			'secret' => $ht_r['secret'],
			'where'  => $ht_r['where'],
			'note'   => $ht_r['note'],
		);
	}
	echo wp_json_encode( $ht_js );
	?>;
	var TXT = <?php
	echo wp_json_encode(
		array(
			'fills'    => __( 'Filled in for you: server %host%, port %port%, %enc%.', 'horse-tools' ),
			'userFix'  => __( 'The account name for this service is the literal word “%user%”. It is not your email address, and putting your address there is why it says the password is wrong.', 'horse-tools' ),
			'secret'   => __( 'What you still have to paste: %secret%.', 'horse-tools' ),
			'getIt'    => __( 'Where to get it', 'horse-tools' ),
			'fromUser' => __( 'Send as the same address you sign in with. This service refuses anything else.', 'horse-tools' ),
			'fromVer'  => __( 'You may send as any address on a domain you have verified with this service. An unverified one will be refused.', 'horse-tools' ),
		)
	);
	?>;

	var sel  = document.getElementById('ht-mail-preset');
	var help = document.getElementById('ht-preset-help');
	if (!sel || !help) { return; }

	var field = function (name) {
		return document.querySelector('[name="horsetools_settings[' + name + ']"]');
	};
	var setVal = function (name, value) {
		var el = field(name);
		if (!el) { return; }
		el.value = value;
		el.dispatchEvent(new Event('change', {bubbles: true}));
	};
	// The change event is what reveals the block a switch controls, so it has to
	// be dispatched rather than just setting .checked — otherwise the settings
	// turn on behind a panel that stays shut.
	var tick = function (name) {
		var el = field(name);
		if (!el || el.type !== 'checkbox' || el.checked) { return; }
		el.checked = true;
		el.dispatchEvent(new Event('change', {bubbles: true}));
	};

	var draw = function (key, apply) {
		var p = PRESETS[key];

		// This described what was stored before anything was picked. Once
		// something is picked it contradicts the help directly below it — and
		// picking the blank entry again makes it true once more.
		if (apply) {
			var looks = document.getElementById('ht-preset-looks');
			if (looks) { looks.style.display = p ? 'none' : ''; }
		}

		if (!p) { help.innerHTML = ''; return; }

		if (apply) {
			setVal('mail-gsmtp15', p.host);
			setVal('mail-gsmtp16', p.port);
			setVal('mail-gsmtp17', p.enc);
			tick('mail-gsmtp18');
			tick('mail-gsmtp1');
			// The master switch for the whole Email module. Leaving it alone was
			// a way to end up with eight perfect settings and nothing sending —
			// which is the exact failure this screen exists to prevent. Choosing
			// a service is not an ambiguous signal about whether you want mail on.
			tick('mail');
			if (p.user) { setVal('mail-gsmtp13', p.user); }
		}

		var enc = p.enc === 'ssl' ? 'SSL' : (p.enc === 'tls' ? 'STARTTLS' : '—');
		var html = '<p class="ht-note"><i class="ti ti-check"></i> '
			+ TXT.fills.replace('%host%', '<code>' + p.host + '</code>')
			           .replace('%port%', '<code>' + p.port + '</code>')
			           .replace('%enc%', enc) + '</p>';

		if (p.user) {
			html += '<p class="ht-note ht-note-red"><i class="ti ti-alert-triangle"></i> '
				+ TXT.userFix.replace('%user%', '<code>' + p.user + '</code>') + '</p>';
		}
		html += '<p class="ht-note"><i class="ti ti-key"></i> '
			+ TXT.secret.replace('%secret%', '<strong>' + p.secret + '</strong>');
		if (p.where) {
			html += ' <a href="' + p.where + '" target="_blank" rel="noopener nofollow">' + TXT.getIt + ' &rarr;</a>';
		}
		html += '</p>';

		if (p.from === 'user')     { html += '<p class="ht-note"><i class="ti ti-at"></i> ' + TXT.fromUser + '</p>'; }
		if (p.from === 'verified') { html += '<p class="ht-note"><i class="ti ti-at"></i> ' + TXT.fromVer + '</p>'; }
		if (p.note)                { html += '<p class="ht-note"><i class="ti ti-bulb"></i> ' + p.note + '</p>'; }

		help.innerHTML = html;
	};

	// On load, describe without touching anything — the owner has not asked for
	// their saved values to be overwritten just by opening the page.
	draw(sel.value, false);
	sel.addEventListener('change', function () { draw(sel.value, true); });
});
</script>

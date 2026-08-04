<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h3><i class="ti ti-help-circle"></i> <?php _e( 'FAQ schema (rich results)', 'horse-tools' ) ?></h3>
	<?php horsetools_toggle( 'faq-schema1', __( 'Publish FAQ schema automatically from the FAQ section in a post', 'horse-tools' ), array(
		'tab'     => 'SEO',
		'section' => 'FAQ schema',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'Reads the “frequently asked questions” part your posts already have — a heading that names the section, then one heading per question with its answer below — and publishes the matching JSON-LD so Google can show them as FAQ rich results. Nothing in your posts is changed. If another SEO plugin already publishes FAQ schema for a post, Horse Tools stays out of the way.', 'horse-tools' ); ?></p>

	<?php horsetools_toggle( 'faq-pages', __( 'Apply to Pages as well, not just Posts', 'horse-tools' ), array(
		'tab'     => 'SEO',
		'section' => 'FAQ schema',
		'parent'  => 'faq-schema1',
	) ); ?>

	<?php horsetools_input( 'faq-keys', __( 'Phrases that mark the FAQ section', 'horse-tools' ), array(
		'tab' => 'SEO',
		'section'     => 'FAQ schema',
		'parent'      => 'faq-schema1',
		'placeholder' => 'thường gặp, hỏi đáp, giải đáp, thắc mắc, FAQ, Q&A',
		'description' => __( 'Comma separated. A heading containing any of these opens the FAQ section; every heading below it until the next heading of the same level is treated as a question.', 'horse-tools' ),
	) ); ?>

	<?php horsetools_input( 'faq-cats', __( 'Only in these category IDs', 'horse-tools' ), array(
		'tab' => 'SEO',
		'section'     => 'FAQ schema',
		'parent'      => 'faq-schema1',
		'placeholder' => __( 'leave empty for all categories', 'horse-tools' ),
	) ); ?>

	<?php horsetools_input( 'faq-min', __( 'Minimum number of questions', 'horse-tools' ), array(
		'tab' => 'SEO',
		'section'     => 'FAQ schema',
		'parent'      => 'faq-schema1',
		'type'        => 'number',
		'min'         => 1,
		'max'         => 20,
		'class'       => 'ht-input-small',
		'placeholder' => '2',
		'description' => __( 'A post with fewer questions than this gets no schema. Leave empty to use 2, which is the safer choice.', 'horse-tools' ),
	) ); ?>

	<?php horsetools_input( 'faq-maxlen', __( 'Longest answer to publish (characters)', 'horse-tools' ), array(
		'tab' => 'SEO',
		'section'     => 'FAQ schema',
		'parent'      => 'faq-schema1',
		'type'        => 'number',
		'min'         => 80,
		'max'         => 5000,
		'class'       => 'ht-input-small',
		'placeholder' => '500',
		'description' => __( 'A longer answer is shortened in the schema only — your post is never touched. Leave empty to use 500.', 'horse-tools' ),
	) ); ?>

	<p class="ht-field" data-ht-parent="ht-main-faq-schema1">
		<button type="button" class="ht-priv-btn" id="ht-faq-scan"><i class="ti ti-scan"></i> <?php _e( 'Scan the whole site', 'horse-tools' ); ?></button>
		<button type="button" class="ht-priv-btn" id="ht-faq-flush"><i class="ti ti-refresh"></i> <?php _e( 'Clear cache & re-read', 'horse-tools' ); ?></button>
	</p>
	<div id="ht-faq-scanout" class="ht-note" style="display:none"></div>
	<script>
	(function(){
		var NONCE = <?php echo wp_json_encode( wp_create_nonce( 'horsetools_faq' ) ); ?>;
		var AJAX  = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var I18N  = {
			scanning: <?php echo wp_json_encode( __( 'Scanning…', 'horse-tools' ) ); ?>,
			fail:     <?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>,
			ok:       <?php echo wp_json_encode( __( '%1$d post(s) will publish FAQ schema (%2$s questions on average).', 'horse-tools' ) ); ?>,
			few:      <?php echo wp_json_encode( __( '%d post(s) have a FAQ section but too few questions:', 'horse-tools' ) ); ?>,
			none:     <?php echo wp_json_encode( __( '%d post(s) mention a FAQ but no question could be read — unusual structure:', 'horse-tools' ) ); ?>,
			foreign:  <?php echo wp_json_encode( __( '%d post(s) already get FAQ schema from another plugin, so they are left alone.', 'horse-tools' ) ); ?>,
			skip:     <?php echo wp_json_encode( __( '%d post(s) have no FAQ section.', 'horse-tools' ) ); ?>,
			flushed:  <?php echo wp_json_encode( __( 'Cache cleared. Each post will be read again the next time it is viewed.', 'horse-tools' ) ); ?>
		};
		function post(data, done){
			data.nonce = NONCE;
			var body = Object.keys(data).map(function(k){ return encodeURIComponent(k)+'='+encodeURIComponent(data[k]); }).join('&');
			fetch(AJAX,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body})
				.then(function(r){return r.json();}).then(done).catch(function(){ out.textContent = I18N.fail; });
		}
		var out = document.getElementById('ht-faq-scanout');
		function list(items){
			return '<ul style="margin:4px 0 10px 18px">' + items.map(function(i){
				return '<li><a href="'+i.link+'" target="_blank" rel="noopener">'+i.title+'</a>'+(i.n?' — '+i.n:'')+'</li>';
			}).join('') + '</ul>';
		}
		document.getElementById('ht-faq-scan').addEventListener('click', function(){
			out.style.display='block'; out.textContent = I18N.scanning;
			post({action:'horsetools_faq_scan'}, function(res){
				if(!res || !res.success){ out.textContent = I18N.fail; return; }
				var d = res.data, h = '';
				h += '<p><b>✅ ' + I18N.ok.replace('%1$d', d.ok).replace('%2$s', d.avg) + '</b></p>';
				if (d.fewN)  { h += '<p>⚠️ ' + I18N.few.replace('%d', d.fewN) + '</p>' + list(d.few); }
				if (d.noneN) { h += '<p>⚠️ ' + I18N.none.replace('%d', d.noneN) + '</p>' + list(d.none); }
				if (d.foreign) { h += '<p>➖ ' + I18N.foreign.replace('%d', d.foreign) + '</p>'; }
				h += '<p>➖ ' + I18N.skip.replace('%d', d.skip) + '</p>';
				out.innerHTML = h;
			});
		});
		document.getElementById('ht-faq-flush').addEventListener('click', function(){
			out.style.display='block'; out.textContent = I18N.scanning;
			post({action:'horsetools_faq_flush'}, function(res){
				out.textContent = (res && res.success) ? I18N.flushed : I18N.fail;
			});
		});
	}());
	</script>


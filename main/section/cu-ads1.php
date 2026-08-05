<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_ads_options; ?>
			<div class="ht-card">
			   <h3><i class="ti ti-alert-circle"></i> <?php _e('Forced ad clicks — removed', 'horse-tools') ?></h3>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i>
				<?php _e('This feature was removed in Horse Tools 1.0.0. It opened an affiliate URL in a hidden off-screen window every time a visitor clicked anywhere on the page. That is affiliate cookie stuffing: it deceives your visitors, breaks the terms of every major affiliate network and of Google AdSense, and can get your domain and your ad accounts banned.', 'horse-tools'); ?>
				<br><br>
				<?php _e('If you had it enabled it is now inactive. The AdSense and ads.txt tools on the other tabs are unaffected.', 'horse-tools'); ?>
				</p>
			</div>

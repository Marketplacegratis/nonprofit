<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Basic Troubleshooting Steps</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<ol>
			<li>
				<p class="glsr-heading">Make sure you are using the latest version of Site Reviews.</p>
				<p>Site Reviews is updated frequently with bug patches, security updates, improvements, and new features. If you are not using the latest version and are experiencing problems, chances are good that your problem has already been addressed in the latest version.</p>
			</li>
			<li>
				<p class="glsr-heading">Temporarily switch to an official WordPress Theme.</p>
				<p>Try switching to an official WordPress Theme (i.e. Twenty Seventeen) and then see if you are still experiencing problems with the plugin. If this fixes the problem then there is a compatibility issue with your theme.</p>
			</li>
			<li>
				<p class="glsr-heading">Temporarily deactivate all of your plugins.</p>
				<p>If switching to an official WordPress theme did not fix anything, the final thing to try is to deactivate all of your plugins except for Site Reviews. If this fixes the problem then there is a compatibility issue with one of your plugins.</p>
				<p>To find out which plugin is incompatible with Site Reviews you will need to reactivate your plugins one-by-one until you find the plugin that is causing the problem. If you think that you’ve found the culprit, deactivate it and continue to test the rest of your plugins. Hopefully you won’t find any more but it’s always better to make sure.</p>
				<p>If you find an incompatible theme or plugin, please <em>contact support</em> so we can fix it.</p>
			</li>
		</ol>
	</div>
</div>

<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Common Problems and Solutions</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p class="glsr-heading">The review form is not working, the submit button just spins.</p>
		<p>Does your website have a SSL certificate? If it does, make sure that your website is configured to always use it by using a SSL plugin such as <a href="https://wordpress.org/plugins/really-simple-ssl/">Really Simple SSL</a>. Site Reviews will use HTTPS to submit a review if possible, but if your site has a valid SSL certificate and you are viewing the website using HTTP (instead of HTTPS) then the browser will detect this as a cross-domain request and prevent the review submission from completing.</p>
	</div>
</div>


<div class="glsr-card postbox">
	<div class="glsr-card-header">
		<h3>Contact Support</h3>
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?= __( 'Toggle documentation panel', 'site-reviews' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
	</div>
	<div class="inside">
		<p>The preferred way to get support for Site Reviews is to use the <a href="https://wordpress.org/support/plugin/site-reviews/">WordPress forum</a>, this way all users of the plugin will have a chance to benefit from the solution. However, you may also contact us directly after confirming the following:</p>
		<p class="glsr-card-field">
			<input type="checkbox" id="step-1" class="glsr-support-step">
			<label for="step-1">I have read the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!faq' ); ?>">FAQ</a></code> documentation page.</label>
		</p>
		<p class="glsr-card-field">
			<input type="checkbox" id="step-2" class="glsr-support-step">
			<label for="step-2">I have read the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!shortcodes' ); ?>">Shortcodes</a></code> documentation page.</label>
		</p>
		<p class="glsr-card-field">
			<input type="checkbox" id="step-3" class="glsr-support-step">
			<label for="step-3">I have read the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=documentation#!hooks' ); ?>">Hooks</a></code> documentation page.</label>
		</p>
		<p class="glsr-card-field">
			<input type="checkbox" id="step-4" class="glsr-support-step">
			<label for="step-4">I have completed the <em>Basic Troubleshooting Steps</em> provided above.</label>
		</p>
		<div class="glsr-card-result hidden">
			<p><strong>Please send an email to <a href="mailto:site-reviews@geminilabs.io?subject=Support%20request">site-reviews@geminilabs.io</a> and include the following details:</strong></p>
			<ul>
				<li>A detailed description of the problem you are having and steps to reproduce it.</li>
				<li>Include screenshots if they will help explain the problem.</li>
				<li>Download and attach the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=tools#!system-info' ); ?>">Tools &rarr; System Info</a></code> report to the email.</li>
				<li>Download and attach the <code><a href="<?= admin_url( 'edit.php?post_type=site-review&page=tools#!console' ); ?>">Tools &rarr; Console</a></code> log file to the email (but only if it's not empty!)</li>
			</ul>
		</div>
	</div>
</div>

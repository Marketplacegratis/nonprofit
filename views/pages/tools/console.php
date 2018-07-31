<?php defined( 'WPINC' ) || die; ?>

<textarea id="log-file" class="large-text code glsr-code" rows="20" readonly>{{ console }}</textarea>
<form method="post" class="float-left">
	<input type="hidden" name="{{ id }}[action]" value="download-console">
	<?php wp_nonce_field( 'download-console' ); ?>
	<?php submit_button( __( 'Download Console', 'site-reviews' ), 'primary', '', false ); ?>
</form>
<form method="post" class="float-left">
	<input type="hidden" name="{{ id }}[action]" value="fetch-console">
	<?php wp_nonce_field( 'fetch-console' ); ?>
	<button type="submit" class="glsr-button button" name="fetch-console" id="fetch-console">
		<span data-loading="<?= __( 'Reloading...', 'site-reviews' ); ?>"><?= __( 'Reload', 'site-reviews' ); ?></span>
	</button>
</form>
<form method="post">
	<input type="hidden" name="{{ id }}[action]" value="clear-console">
	<?php wp_nonce_field( 'clear-console' ); ?>
	<button type="submit" class="glsr-button button" name="clear-console" id="clear-console">
		<span data-loading="<?= __( 'Clearing...', 'site-reviews' ); ?>"><?= __( 'Clear', 'site-reviews' ); ?></span>
	</button>
</form>

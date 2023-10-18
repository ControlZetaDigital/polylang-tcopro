<?php
/**
 * Display sidebar.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/admin/views
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="postbox" id="support">

	<h3>

		<span><?php esc_html_e( 'Do you like this plugin?', 'polylang-tcopro' ); ?></span>

	</h3>

	<div class="inside">

		<p>
			<?php
			printf(
				'%s <a href=\'%s\'>%s</a>.',
				esc_html__( 'If you find this plugin useful, I would appreciate a donation to continue improving it. Your support would be greatly appreciated!', 'polylang-tcopro' ),
				esc_url( 'https://controlzetadigital.com/contactar' ),
				esc_html__( 'Contact me to make your donation!', 'polylang-tcopro' )
			);
			?>
		</p>

	</div>

</div>

<div class="postbox" id="update">

	<div class="inside">

		<?php wp_nonce_field( "{$this->plugin_name}_update", "{$this->plugin_name}_update_nonce" ); ?>

		<button class="button button-primary"><?php _e("Update", "polylang-tcopro"); ?></button>

	</div>

</div>
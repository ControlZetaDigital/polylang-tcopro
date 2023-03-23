<?php
/**
 * Display sidebar.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/admin/partials
 */

$update_url  = add_query_arg(
	array(
		'polylang_tcopro_action' => 'update'
	)
);
$nonced_url = wp_nonce_url( $update_url, 'polylang_tcopro-update' );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<a href="<?php echo esc_url( $nonced_url ); ?>" class="button-primary">
	<?php esc_html_e( 'Update', 'polylang-tcopro' ); ?>
</a>
<div class="postbox" id="support">
	<h3 class="hndle">
		<span><?php esc_html_e( 'Need Help?', 'polylang-tcopro' ); ?></span>
	</h3>
	<div class="inside">
		<p>
			<?php
			printf(
				'%s <a href=\'%s\'>%s</a>.',
				esc_html__( 'Please use our', 'polylang-tcopro' ),
				esc_url( 'http://rtcamp.com/support/forum/wordpress-nginx/' ),
				esc_html__( 'free support forum', 'polylang-tcopro' )
			);
			?>
		</p>
	</div>
</div>
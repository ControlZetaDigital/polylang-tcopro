<?php
/**
 * Provide a debug area view for development purposes
 *
 * @link       https://github.com/ControlZetaDigital/polylang-tcopro
 * @since      1.0.0
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/admin/views
 */

global $pagenow;
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap rt-<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-wrapper">

	<h2 class="rt_option_title">

		<?php esc_html_e( 'Polylang Debug', 'polylang-tcopro' ); ?>

	</h2>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2 <?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>">

			<pre></pre>

		</div> <!-- End of #post-body -->

	</div> <!-- End of #poststuff -->

</div> <!-- End of .wrap .rt-nginx-wrapper -->

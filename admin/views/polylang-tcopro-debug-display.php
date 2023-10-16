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

<div class="wrap rt-<?php echo $this->plugin_name; ?>-wrapper">

	<h2 class="rt_option_title">

		<?php esc_html_e( 'Polylang Support', 'polylang-tcopro' ); ?>

	</h2>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2 <?php echo $this->plugin_name; ?>">

			<pre>

				<?php 
				
				foreach($ptco->get_layouts("archive") as $layout) : ?>

					<?php print_r([
						"ID" => $layout->ID,
						"title" => $layout->title,
						"assignments" => $layout->assignments,
						"priority" => $layout->priority
					]);
					
					?>

				<?php endforeach; ?>

			</pre>

		</div> <!-- End of #post-body -->

	</div> <!-- End of #poststuff -->

</div> <!-- End of .wrap .rt-nginx-wrapper -->

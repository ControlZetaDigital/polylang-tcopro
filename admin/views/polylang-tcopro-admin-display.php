<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
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

			<form action="" method="post">

				<div id="post-body-content" class="<?php echo $this->plugin_name; ?>-widgets">				

					<?php foreach($widgets as $widget) : ?>

					<div class="<?php echo $this->plugin_name; ?>-widget <?php echo $widget->slug; ?>" data-widget="<?php echo $widget->slug; ?>">

						<h3><?php echo $widget->title; ?></h3>

						<?php require plugin_dir_path( __FILE__ ) . 'partials/widget-assignments.php'; ?>

					</div>

					<?php endforeach; ?>				

				</div> <!-- End of #post-body-content -->

				<div id="postbox-container-1" class="postbox-container">

					<?php require plugin_dir_path( __FILE__ ) . 'polylang-tcopro-sidebar-display.php'; ?>

				</div> <!-- End of #postbox-container-1 -->

			</form>

		</div> <!-- End of #post-body -->

	</div> <!-- End of #poststuff -->

</div> <!-- End of .wrap .rt-nginx-wrapper -->

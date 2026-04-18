<?php defined( 'WPINC' ) || die; ?>

<div class="wrap rt-<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-wrapper">

	<h2 class="rt_option_title">
		<?php esc_html_e( 'Polylang Support', 'polylang-tcopro' ); ?>
	</h2>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2 <?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>">

			<form action="" method="post">

				<div id="post-body-content" class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-widgets">

					<?php foreach ( $widgets as $widget ) : ?>

					<div class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-widget <?php echo esc_attr( $widget->slug ); ?>" data-widget="<?php echo esc_attr( $widget->slug ); ?>">

						<h3><?php echo esc_html( $widget->title ); ?></h3>

						<?php require __DIR__ . '/partials/widget-assignments.php'; ?>

					</div>

					<?php endforeach; ?>

				</div>

				<div id="postbox-container-1" class="postbox-container">

					<?php require __DIR__ . '/partials/sidebar.php'; ?>

				</div>

			</form>

		</div>

	</div>

</div>

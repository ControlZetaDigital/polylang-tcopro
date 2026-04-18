<?php defined( 'WPINC' ) || die;

$sections = [
	'Headers'         => $ptco->getHeaders(),
	'Footers'         => $ptco->getFooters(),
	'Archive layouts' => $ptco->getLayouts( 'archive' ),
	'Single layouts'  => $ptco->getLayouts( 'single' ),
];
?>

<div class="wrap rt-<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-wrapper">

	<h2><?php esc_html_e( 'Polylang Debug', 'polylang-tcopro' ); ?></h2>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder <?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>">

			<h3>Languages</h3>
			<pre><?php echo esc_html( print_r( $ptco->getLanguages(), true ) ); ?></pre>

			<?php foreach ( $sections as $label => $items ) : ?>
				<h3><?php echo esc_html( $label ); ?></h3>

				<?php if ( empty( $items ) ) : ?>
					<p><em>No items found.</em></p>
				<?php else : ?>
					<table class="widefat striped" style="margin-bottom:2em">
						<thead>
							<tr>
								<th>ID</th>
								<th>Title</th>
								<th>Language assignment (plugin)</th>
								<th>CS assignments</th>
								<th>Priority</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $items as $item ) : ?>
							<?php $lang_data = $ptco->getItemLanguages( $item->ID ); ?>
							<tr>
								<td><?php echo absint( $item->ID ); ?></td>
								<td><?php echo esc_html( $item->title ); ?></td>
								<td>
									<?php if ( $lang_data ) : ?>
										<code><?php echo esc_html( $lang_data->value ); ?></code>
									<?php else : ?>
										<em style="color:#999">none</em>
									<?php endif; ?>
								</td>
								<td><pre style="margin:0;font-size:11px"><?php echo esc_html( print_r( $item->assignments, true ) ); ?></pre></td>
								<td><?php echo absint( $item->priority ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			<?php endforeach; ?>

		</div>
	</div>

</div>

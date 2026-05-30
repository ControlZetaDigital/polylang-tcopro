<?php defined( 'WPINC' ) || die; ?>

<div class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-widget-assignments">

	<?php foreach ( $widget->items as $item ) : // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template-scoped variables passed via include ?>

		<?php $item_languages = $ptco->getItemLanguages( $item->ID ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>

		<div class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-assignment">

			<div class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-assignment-item">

				<h4>
					<a target="_blank" href="<?php echo esc_url( home_url( "/cornerstone/edit/{$item->ID}" ) ); ?>">
						<span class="dashicons dashicons-edit"></span>
					</a>
					<?php echo esc_html( $item->title ); ?> (Id: <?php echo absint( $item->ID ); ?>)
				</h4>

				<ul class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-language-list">

					<?php foreach ( $languages as $lang ) : // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>

						<?php
						$default  = ( $lang['slug'] === $ptco->defaultLanguage() ) ? ' default' : ''; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
						$selected = ( $item_languages && in_array( $lang['slug'], $item_languages->list, true ) ) ? ' selected' : ''; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
						?>

						<li>
							<a class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-flag<?php echo esc_attr( $default . $selected ); ?>"
							   data-language="<?php echo esc_attr( $lang['slug'] ); ?>">
								<img src="<?php echo esc_url( $lang['flag'] ); ?>" width="18" height="18" />
							</a>
						</li>

					<?php endforeach; ?>

				</ul>

			</div>

			<input type="hidden"
			       name="item_<?php echo absint( $item->ID ); ?>_languages"
			       <?php echo $item_languages ? 'value="' . esc_attr( $item_languages->value ) . '"' : ''; ?> />

		</div>

	<?php endforeach; ?>

</div>

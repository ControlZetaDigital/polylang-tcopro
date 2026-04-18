<?php
/**
 * Widget item list and language assignments
 *
 *
 * @link       https://github.com/ControlZetaDigital/polylang-tcopro
 * @since      1.1.0
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/admin/views/partials
 */

?>

<div class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-widget-assignments">

    <?php foreach($widget->items as $item) : ?>

        <?php $item_languages = $ptco->getItemLanguages($item->ID); ?>

        <div class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-assignment">

            <div class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-assignment-item">

                <h4><a target="_blank" href="<?php echo esc_url( home_url( "/cornerstone/edit/{$item->ID}" ) ); ?>"><span class="dashicons dashicons-edit"></span></a> <?php echo esc_html( $item->title ); ?> (Id: <?php echo absint( $item->ID ); ?>)</h4>

                <ul class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-language-list">

                <?php foreach($languages as $lang) : ?>

                    <li>

                        <?php $default = ($lang["slug"] === $ptco->defaultLanguage()) ? ' default' : ''; ?>

                        <?php $selected = ($item_languages && in_array($lang["slug"], $item_languages->list, true)) ? ' selected' : ''; ?>

                        <a class="<?php echo esc_attr( POLYLANG_TCOPRO_NAME ); ?>-flag<?php echo esc_attr( $default . $selected ); ?>" data-language="<?php echo esc_attr( $lang["slug"] ); ?>">

                            <img src="<?php echo esc_url( $lang["flag"] ); ?>" width="18" height="18" />

                        </a>

                    </li>

                <?php endforeach; ?>

                </ul>

            </div>

            <input type="hidden" name="item_<?php echo absint( $item->ID ); ?>_languages" <?php echo $item_languages ? 'value="' . esc_attr( $item_languages->value ) . '" ' : ''; ?>/>

        </div>

    <?php endforeach; ?>

</div>
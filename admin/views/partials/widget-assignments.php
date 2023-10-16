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

<div class="<?php echo $this->plugin_name; ?>-widget-assignments">

    <?php foreach($widget->items as $item) : ?>

        <?php $item_languages = $ptco->get_item_languages($item->ID); ?>

        <div class="<?php echo $this->plugin_name; ?>-assignment">

            <div class="<?php echo $this->plugin_name; ?>-assignment-item">

                <h4><a target="_blank" href="<?php echo home_url("/cornerstone/edit/{$item->ID}"); ?>"><span class="dashicons dashicons-edit"></span></a> <?php echo $item->title; ?> (Id: <?php echo $item->ID; ?>)</h4>

                <ul class="<?php echo $this->plugin_name; ?>-language-list">

                <?php foreach($languages as $lang) : ?>

                    <li>                        

                        <?php $default = ($lang["slug"] === $ptco->default_language()) ? ' default' : ''; ?>

                        <?php $selected = ($item_languages && in_array($lang["slug"], $item_languages->list)) ? ' selected' : ''; ?>

                        <a class="<?php echo $this->plugin_name; ?>-flag<?php echo $default . $selected; ?>" data-language="<?php echo $lang["slug"]; ?>">
                        
                            <img src="<?php echo $lang["flag"]; ?>" width="18" height="18" />
                        
                        </a>

                    </li>

                <?php endforeach; ?>

                </ul>

            </div>

            <input type="hidden" name="item_<?php echo $item->ID; ?>_languages" <?php echo ($item_languages) ? 'value="'.$item_languages->value.'" ' : ''; ?>/>

        </div>

    <?php endforeach; ?>

</div>
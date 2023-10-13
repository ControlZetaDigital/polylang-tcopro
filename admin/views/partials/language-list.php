<?php
/**
 * Language list partial
 *
 * Partial showing a list with each active language on the site.
 *
 * @link       http://example.com
 * @since      1.0.1
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/admin/views/partials
 */

?>

<ul class="<?php echo $this->plugin_name; ?>-language-list">

    <?php foreach($languages as $lang) : ?>

        <li class="<?php echo $this->plugin_name; ?>-lang<?php echo ($lang["slug"] === $ptco->default_language()) ? ' default' : ''; ?>">

            <img src="<?php echo $lang["flag"]; ?>" width="22" height="22" />

            <?php $field_name = $widget->slug . "_" . $lang["slug"]; ?>

            <?php $option_selected = (get_option("{$this->plugin_name}_{$field_name}")) ? get_option("{$this->plugin_name}_{$field_name}") : false; ?>

            <select class="<?php echo $this->plugin_name; ?>-item-select" name="<?php echo $field_name; ?>">

                <option value="0">-- <?php echo $widget->title; ?> --</option>

                <?php foreach($widget->items as $item) : ?>

                    <option value="<?php echo $item->ID; ?>"<?php echo ($option_selected && $option_selected == $item->ID) ? " selected" : ""; ?>>
                        <?php echo $item->title; ?> (Id: <?php echo $item->ID; ?>)</option>

                <?php endforeach; ?>

            </select>

        </li>

    <?php endforeach; ?>

</ul>
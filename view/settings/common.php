<form method="post">
    <input type="hidden" name="setting_form" value="1"/>
    

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Import Settings', 'Setting title', 'bng2woo-lite');?></h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Language', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("It's applied to Product title, description, attributes", 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $cur_language = b2wl_get_setting('import_language');?>
                        <select name="b2w_import_language" id="b2w_import_language" class="form-control small-input">
                            <option value="en" <?php if ($cur_language == "en"): ?>selected="selected"<?php endif;?>>English</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Currency', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Default currency that used on a product import", 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $cur_b2w_local_currency = strtoupper(b2wl_get_setting('local_currency'));?>
                        <select name="b2w_local_currency" id="b2w_local_currency" class="form-control small-input">
                            <?php foreach ($currencies as $code => $name): ?><option value="<?php echo esc_attr($code); ?>" <?php if ($cur_b2w_local_currency == $code): ?>selected="selected"<?php endif;?>><?php echo esc_html($name); ?></option><?php endforeach;?>
                            <?php if (!empty($custom_currencies)): ?>
                            <?php foreach ($custom_currencies as $code => $name): ?><option value="<?php echo esc_attr($code); ?>" <?php if ($cur_b2w_local_currency == $code): ?>selected="selected"<?php endif;?>><?php echo esc_html($name); ?></option><?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_default_product_type">
                        <strong><?php _ex('Default product type', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Default product type", 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $default_product_type = b2wl_get_setting('default_product_type');?>
                        <select name="b2wl_default_product_type" id="b2wl_default_product_type" class="form-control small-input">
                            <option value="simple" <?php if ($default_product_type == "simple"): ?>selected="selected"<?php endif;?>><?php _ex('Simple/Variable Product', 'Setting option', 'bng2woo-lite');?></option>
                            <option value="external" <?php if ($default_product_type == "external"): ?>selected="selected"<?php endif;?>><?php _ex('External/Affiliate Product', 'Setting option', 'bng2woo-lite');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_default_product_status">
                        <strong><?php _ex('Default product status', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex("Default product type", 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $default_product_status = b2wl_get_setting('default_product_status');?>
                        <select name="b2wl_default_product_status" id="b2wl_default_product_status" class="form-control small-input">
                            <option value="publish" <?php if ($default_product_status == "publish"): ?>selected="selected"<?php endif;?>><?php _e('Publish');?></option>
                            <option value="draft" <?php if ($default_product_status == "draft"): ?>selected="selected"<?php endif;?>><?php _e('Draft');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_not_import_description">
                        <strong><?php _e('Not import description', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Not import description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="b2wl_not_import_description" name="b2wl_not_import_description" value="yes" <?php if (b2wl_get_setting('not_import_description')): ?>checked<?php endif;?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_use_external_image_urls">
                        <strong><?php _ex('Use external image urls', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Use external image urls', 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="b2wl_use_external_image_urls" name="b2wl_use_external_image_urls" value="yes" <?php if (b2wl_get_setting('use_external_image_urls')): ?>checked<?php endif;?>/>
                    </div>
                    <div id="b2wl_load_external_image_block" class="form-group input-block no-margin" <?php if (b2wl_get_setting('use_external_image_urls')): ?>style="display: none;"<?php endif;?>>
                        <input class="btn btn-default load-images" disabled="disabled" type="button" value="<?php _e('Load images', 'bng2woo-lite');?>"/>
                        <div id="b2wl_load_external_image_progress"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_background_import">
                        <strong><?php _ex('Import in the background', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Products will be imported in the background mode, make sure you CRON is enabled.', 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="b2wl_background_import" name="b2wl_background_import" value="yes" <?php if (b2wl_get_setting('background_import')): ?>checked<?php endif;?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_convert_attr_case">
                        <strong><?php _ex('Convert case of attributes and their values', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Products may come with different text case of attributes and their values. ', 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $convert_attr_case = b2wl_get_setting('convert_attr_case');?>
                        <select name="b2wl_convert_attr_case" id="b2wl_convert_attr_case" class="form-control small-input">
                            <option value="original" <?php if ($convert_attr_case == "original"): ?>selected="selected"<?php endif;?>><?php _e('Keep original case');?></option>
                            <option value="lower" <?php if ($convert_attr_case == "lower"): ?>selected="selected"<?php endif;?>><?php _e('Lower case');?></option>
                            <option value="sentence" <?php if ($convert_attr_case == "sentence"): ?>selected="selected"<?php endif;?>><?php _e('Sentence case');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="_b2wfo b2wl-info"><div>This feature is available in the full plugin version.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
            <div class="row _b2wfv">
                <div class="col-md-4">
                    <label for="b2wl_remove_ship_from">
                        <strong><?php echo esc_html_x('Remove "Ship From" attribute', 'Setting title', 'bng2woo-lite'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('Remove Ship from attribute during product import.', 'setting description', 'bng2woo-lite'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="b2wl_remove_ship_from" name="b2wl_remove_ship_from" value="yes" <?php if (b2wl_get_setting('remove_ship_from')): ?>checked<?php endif;?>/>
                    </div>
                </div>
            </div>


            <div id="b2wl_remove_ship_from_block" class="row _b2wfv" <?php if (!b2wl_get_setting('remove_ship_from')): ?>style="display: none;"<?php endif;?>>
                <div class="col-md-4">
                    <label for="b2wl_default_ship_from">
                        <strong><?php echo esc_html_x('Default "Ship From" country', 'Setting title', 'bng2woo-lite'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('Only product variations that contain Ship from selected country will be imported.', 'setting description', 'bng2woo-lite'); ?>"></div>
                </div>
                <div class="col-md-6">

                        <?php $cur_b2wl_default_ship_from = b2wl_get_setting('default_ship_from');?>
                        <select name="b2wl_default_ship_from" id="b2wl_default_ship_from" class="form-control small-input country_list">
                            <?php foreach ($shipping_countries as $country): ?>
                                <option value="<?php echo $country['c']; ?>"<?php if ($cur_b2wl_default_ship_from == $country['c']): ?> selected<?php endif;?>>
                                    <?php echo $country['n']; ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                        <div class="input-block"><?php echo esc_html_x('Note! If the "Ship From" attribute does not contain the selected country, then China will be used as the "Ship from" country.', 'setting description', 'bng2woo-lite'); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_default_stock">
                        <strong><?php echo esc_html_x('Set default stock value', 'Setting title', 'bng2woo-lite'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title='<?php esc_html_e('Sometimes Banggood API doesn`t return stock quantity, in this case the plugin will use the default value.', 'bng2woo-lite');?>'></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="text" class="form-control small-input" style="max-width: 60px;" id="b2wl_default_stock" name="b2wl_default_stock" value="<?php echo esc_attr(b2wl_get_setting('default_stock')); ?>" <?php if (b2wl_get_setting('use_random_stock')): ?>disabled<?php endif;?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_use_random_stock">
                        <strong><?php echo esc_html_x('Use random stock value', 'Setting title', 'bng2woo-lite'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('Use random stock value', 'setting description', 'bng2woo-lite'); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="b2wl_use_random_stock" name="b2wl_use_random_stock" value="yes" <?php if (b2wl_get_setting('use_random_stock')): ?>checked<?php endif;?>/>
                    </div>
                    <div id="b2wl_use_random_stock_block" class="form-group input-block no-margin" <?php if (!b2wl_get_setting('use_random_stock')): ?>style="display: none;"<?php endif;?>>
                        <?php esc_html_e('From', 'bng2woo-lite');?> <input type="text" style="max-width: 60px;" class="form-control" id="b2wl_use_random_stock_min" name="b2wl_use_random_stock_min" value="<?php echo esc_attr(b2wl_get_setting('use_random_stock_min')); ?>">
                        <?php esc_html_e('To', 'bng2woo-lite');?> <input type="text" style="max-width: 60px;" class="form-control" id="b2wl_use_random_stock_max" name="b2wl_use_random_stock_max" value="<?php echo esc_attr(b2wl_get_setting('use_random_stock_max')); ?>">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="display-inline"><?php _ex('Schedule settings', 'Setting title', 'bng2woo-lite');?></h3>
        </div>
        <div class="_b2wfo b2wl-info"><div>This feature is available in the full plugin version.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
        <div class="panel-body _b2wfv">
            <?php $b2wl_auto_update = b2wl_get_setting('auto_update');?>
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php _ex('Banggood Sync', 'Setting title', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _ex('Enable auto-update features', 'setting description', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <input type="checkbox" class="form-control" id="b2wl_auto_update" name="b2wl_auto_update" value="yes" <?php if ($b2wl_auto_update): ?>checked<?php endif;?>/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_on_not_available_product">
                        <strong><?php _e('When product is no longer available', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when one of your products is no longer available from Banggood. Applies to all existing products.', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_not_available_product = b2wl_get_setting('on_not_available_product');?>
                        <select class="form-control small-input" name="b2wl_on_not_available_product" id="b2wl_on_not_available_product" <?php if (!$b2wl_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_not_available_product == "nothing"): ?>selected="selected"<?php endif;?>><?php _e('Do Nothing', 'bng2woo-lite');?></option>
                            <option value="trash" <?php if ($on_not_available_product == "trash"): ?>selected="selected"<?php endif;?>><?php _e('Move to trash', 'bng2woo-lite');?></option>
                            <option value="zero" <?php if ($on_not_available_product == "zero"): ?>selected="selected"<?php endif;?>><?php _e('Set Quantity To Zero', 'bng2woo-lite');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_on_not_available_variation">
                        <strong><?php _e('When variant is no longer available', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when one of the product’s variants is no longer available from Banggood.', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_not_available_variation = b2wl_get_setting('on_not_available_variation');?>
                        <select class="form-control small-input" name="b2wl_on_not_available_variation" id="b2wl_on_not_available_variation" <?php if (!$b2wl_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_not_available_variation == "nothing"): ?>selected="selected"<?php endif;?>><?php _e('Do Nothing', 'bng2woo-lite');?></option>
                            <option value="trash" <?php if ($on_not_available_variation == "trash"): ?>selected="selected"<?php endif;?>><?php _e('Remove variant', 'bng2woo-lite');?></option>
                            <option value="zero" <?php if ($on_not_available_variation == "zero"): ?>selected="selected"<?php endif;?>><?php _e('Set Quantity To Zero', 'bng2woo-lite');?></option>
                            <option value="zero_and_disable" <?php if ($on_not_available_variation == "zero_and_disable"): ?>selected="selected"<?php endif;?>><?php _e('Set Quantity To Zero and Disable', 'bng2woo-lite');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_on_new_variation_appearance">
                        <strong><?php _e('When a new variant has appeared', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when new of the product’s variants is an appearance on Banggood.', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_new_variation_appearance = b2wl_get_setting('on_new_variation_appearance');?>
                        <select class="form-control small-input" name="b2wl_on_new_variation_appearance" id="b2wl_on_new_variation_appearance" <?php if (!$b2wl_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_new_variation_appearance == "nothing"): ?>selected="selected"<?php endif;?>><?php _e('Do Nothing', 'bng2woo-lite');?></option>
                            <option value="add" <?php if ($on_new_variation_appearance == "add"): ?>selected="selected"<?php endif;?>><?php _e('Add variant', 'bng2woo-lite');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_on_price_changes">
                        <strong><?php _e('When the price changes', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when the price of your product changes.', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_price_changes = b2wl_get_setting('on_price_changes');?>
                        <select class="form-control small-input" name="b2wl_on_price_changes" id="b2wl_on_price_changes" <?php if (!$b2wl_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_price_changes == "nothing"): ?>selected="selected"<?php endif;?>><?php _e('Do Nothing', 'bng2woo-lite');?></option>
                            <option value="update" <?php if ($on_price_changes == "update"): ?>selected="selected"<?php endif;?>><?php _e('Update price', 'bng2woo-lite');?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="b2wl_on_stock_changes">
                        <strong><?php _e('When inventory changes', 'bng2woo-lite');?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php _e('Choose an action when the inventory level of a particular product changes.', 'bng2woo-lite');?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-block no-margin">
                        <?php $on_stock_changes = b2wl_get_setting('on_stock_changes');?>
                        <select class="form-control small-input" name="b2wl_on_stock_changes" id="b2wl_on_stock_changes" <?php if (!$b2wl_auto_update): ?>disabled<?php endif;?>>
                            <option value="nothing" <?php if ($on_stock_changes == "nothing"): ?>selected="selected"<?php endif;?>><?php _e('Do Nothing', 'bng2woo-lite');?></option>
                            <option value="update" <?php if ($on_stock_changes == "update"): ?>selected="selected"<?php endif;?>><?php _e('Update automatically', 'bng2woo-lite');?></option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="row pt20 border-top">
            <div class="col-sm-12">
                <input class="btn btn-success js-main-submit" type="submit" value="<?php _e('Save settings', 'bng2woo-lite');?>"/>
            </div>
        </div>
    </div>

</form>

<script>

    function b2wl_isInt(value) {
        return !isNaN(value) &&
                parseInt(Number(value)) == value &&
                !isNaN(parseInt(value, 10));
    }

    (function ($) {
        if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }
         
        $("#b2wl_use_random_stock").change(function () {
            $("#b2wl_default_stock").prop('disabled', $(this).is(':checked'));
            $("#b2wl_use_random_stock_block").toggle();
            return true;
        });
         

        var b2wl_import_product_images_limit_keyup_timer = false;

        $('#b2wl_import_product_images_limit').on('keyup', function () {
            if (b2wl_import_product_images_limit_keyup_timer) {
                clearTimeout(b2wl_import_product_images_limit_keyup_timer);
            }

            var this_el = $(this);

            this_el.parents('.form-group').removeClass('has-error');
            if (this_el.parents('.form-group').children('span').length > 0)
                this_el.parents('.form-group').children('span').remove();

            b2wl_import_product_images_limit_keyup_timer = setTimeout(function () {
                if (!b2wl_isInt(this_el.val()) || this_el.val() < 0) {
                    this_el.after("<span class='help-block'>Please enter a integer greater than or equal to 0</span>");
                    this_el.parents('.form-group').addClass('has-error');
                }

            }, 1000);
        });

        //form submit
        $('.b2wl-content form').on('submit', function () {
            if ($(this).find('.has-error').length > 0)
                return false;
        })
    })(jQuery);


</script>

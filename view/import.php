<div class="b2wl-content">
    <div class="container">
        <div class="_b2wfo b2wl-info"><div>You are using Lite version of the plugin. If you want to unlock all features and get premium support, purchase the full version of the plugin.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
        <?php include_once B2WL()->plugin_path() . '/view/chrome_notify.php';?>
        

        <div id="b2wl-import-empty" class="panel panel-default margin-top"<?php if ($serach_query || count($product_list) !== 0): ?> style="display:none;"<?php endif;?>>
            <div class="panel-body">
                <?php _e('Your Import List is Empty.', 'bng2woo-lite');?>
                <br/>
                <br/>
                <?php _e('You can add products to this list from the “Search Products” page.', 'bng2woo-lite');?>
            </div>
        </div>


        <div id="b2wl-import-content"<?php if (!$serach_query && count($product_list) === 0): ?> style="display:none;"<?php endif;?>>
            <div id="b2wl-import-filter">
                <div class="row">
                    <div class="col-md-6">
                        <h3><?php _e('Import List', 'bng2woo-lite');?></h3>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="<?php echo esc_url(admin_url('admin.php')); ?>">
                            <input type="hidden" name="page" value="b2wl_import"/>
                            <table class="float-right">
                                <tr>
                                    <td class="padding-small-right">
                                        <select class="form-control" name="o" style="padding-right: 25px;">
                                            <?php foreach ($sort_list as $k => $v): ?><option value="<?php echo esc_attr($k); ?>"<?php if ($sort_query === $k): ?> selected="selected"<?php endif;?>><?php echo esc_html($v); ?></option><?php endforeach;?>
                                        </select>
                                    </td>
                                    <td class="padding-small-right"><input type="search" name="s" class="form-control" value="<?php echo esc_attr($serach_query); ?>"></td>
                                    <td><input type="submit" class="btn btn-default" value="<?php _e('Search products', 'bng2woo-lite');?>"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <div id="b2wl-import-actions">
                <div class="row">
                    <div class="col-lg-5 col-md-12 space-top">
                        <div class="container-flex" style="height: 32px;">
                            <div class="margin-right">
                                <input type="checkbox" class="check-all form-control"><span class="space-small-left"><strong><?php _e('Select All Products', 'bng2woo-lite');?></strong></span>
                            </div>
                            <div class="action-with-check" style="display: none;">
                                <select class="form-control">
                                    <option value="0">Bulk Actions (0 selected)</option>
                                    <option value="remove"><?php _e('Remove from Import List', 'bng2woo-lite');?></option>
                                    <option value="push"><?php _e('Push Products to Shop', 'bng2woo-lite');?></option>
                                    <option value="link-category"><?php _e('Link to category', 'bng2woo-lite');?></option>
                                </select>
                                <div class="loader"><div class="b2wl-loader"></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 space-top align-right">
                        <a href="#" class="btn btn-default link_category_all"><?php _e('Link All products to category', 'bng2woo-lite');?></a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=' . $_REQUEST['page']) . '&action=delete_all'); ?>" class="btn btn-danger margin-small-left delete_all"><?php _e('Remove All Products', 'bng2woo-lite');?></a>
                        <button type="button" class="btn btn-success no-outline btn-icon-left margin-small-left push_all">
                            <div class="btn-loader-wrap"><div class="b2wl-loader"></div></div>
                            <span class="btn-icon-wrap add"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-add"></use></svg></span><?php _e('Push All Products to Shop', 'bng2woo-lite');?>
                        </button>
                    </div>

                </div>
            </div>

            <div class="panel panel-default margin-top"<?php if (count($product_list) !== 0): ?> style="display:none;"<?php endif;?>>
                <div class="panel-body">
                    <?php _e('No products found.', 'bng2woo-lite');?>
                </div>
            </div>

            <div class="b2wl-product-import-list">
                <?php foreach ($product_list as $product): ?>
                    <div class='row space-top'>
                        <div class='col-xs-12'>
                        <?php $product['shipping_from_country_list'] = isset($product['shipping_from_country_list']) ? array_map('esc_attr', $product['shipping_from_country_list']) : '';?>
                        <div class='product<?php echo isset($product['shipping_cost']) ? " shiping_loaded" : "" ?>' data-id="<?php echo esc_attr($product['import_id']); ?>" data-country_from_list="<?php echo empty($product['shipping_from_country_list']) ? '' : implode(";", $product['shipping_from_country_list']); ?>" data-country_from="<?php echo isset($product['shipping_from_country']) ? $product['shipping_from_country'] : ''; ?>" data-country_to="<?php echo isset($product['shipping_to_country']) ? esc_attr($product['shipping_to_country']) : ''; ?>">
                                <div class="b2wl-row">
                                    <ul class="nav nav-tabs">
                                        <li class="select darker-background"><span class="for-checkbox"><input type="checkbox" class="form-control" value="<?php echo esc_attr($product['import_id']); ?>"></span></li>
                                        <li class="active"><a href="#" rel="product"><?php _e('Product', 'bng2woo-lite');?></a></li>
                                        <li> <a href="#" rel="description"><?php _e('Description', 'bng2woo-lite');?></a></li>
                                        <li <?php if (b2wl_check_defined('B2WL_DO_NOT_IMPORT_VARIATIONS')): ?>style="display:none;"<?php endif;?>> <a href="#" rel="variants"><?php _e('Variants', 'bng2woo-lite');?> <span class="badge badge-tab margin-small-left variants-count"><?php echo count($product['sku_products']['variations']) ?></span></a></li>
                                        <li> <a href="#" rel="images"><?php _e('Images', 'bng2woo-lite');?></a></li>
                                    </ul>
                                    <div class="actions">
                                        <span class="margin-small-right"><?php _e('External Id', 'bng2woo-lite');?>: <b><?php echo esc_html($product['id']); ?></b></span>
                                        <div class="btn-group margin-small-right">
                                            <button type="button" class="btn btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?php _e('Action', 'bng2woo-lite');?> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <?php if (false && empty($product['override_product_id'])): ?>
                                                    <li><a href="#" class="product-card-override-product"><?php _e('Select Product to Override', 'bng2woo-lite');?></a></li>
                                                <?php endif;?>
                                                <?php if (false && count($product['sku_products']['variations']) > 1): ?>
                                                    <li><a href="#" class="product-card-split-product"><?php _e('Split Product', 'bng2woo-lite');?></a></li>
                                                <?php endif;?>
                                                <li><a href="<?php echo esc_url(admin_url('admin.php?page=' . $_REQUEST['page']) . '&delete_id=' . $product['import_id']); ?>"><?php _e('Remove Product', 'bng2woo-lite');?></a></li>
                                            </ul>
                                        </div>

                                        <button type="button" class="btn btn-success no-outline btn-icon-left margin-right post_import">
                                            <div class="btn-loader-wrap"><div class="b2wl-loader"></div></div>
                                            <span class="btn-icon-wrap add"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-add"></use></svg></span>
                                            <span class="btn-title"><?php echo isset($product['override_product_id']) ? __('Override', 'bng2woo-lite') : __('Push to Shop', 'bng2woo-lite'); ?></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="tabs-content active" rel="product">
                                    <div class="product-info-container">
                                        <?php if (isset($product['override_product_id'])): ?>
                                            <div class="b2wl-warning">
                                                <?php echo $override_model->override_message($product['override_product_id'], $product['override_product_title']); ?>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-xs-4">
                                            <div class="product-img">
                                                <img class="border-img lazy" src="<?php echo esc_url(B2WL()->plugin_url() . '/assets/img/blank_image.png'); ?>" data-id="<?php echo md5($product['thumb']); ?>" data-original="<?php echo esc_url(isset($product['tmp_edit_images'][md5($product['thumb'])]['attachment_url']) ? $product['tmp_edit_images'][md5($product['thumb'])]['attachment_url'] : b2wl_image_url($product['thumb'])); ?>" alt="<?php echo esc_attr($product['title']); ?>">
                                                <?php if (isset($product['is_affiliate']) && $product['is_affiliate']): ?><div class="affiliate-icon"></div><?php endif;?>
                                            </div>
                                        </div>
                                        <div class="col-md-9 col-xs-8">
                                            <div class="container-flex">
                                                <div class="mr10 no-shrink ali-supplier"></div>
                                                <h3>
                                                    <a class="blue-color" href="<?php echo esc_url($product['url']); ?>" target="_blank">
                                                        <?php echo esc_html(isset($product['original_title']) ? $product['original_title'] : $product['title']); ?>
                                                    </a>
                                                    <span class="red-color"></span>
                                                </h3>
                                            </div>
                                            <?php if (!empty($product['seller_url'])): ?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        by <a class="blue-color" href="<?php echo esc_url($product['seller_url']); ?>" target="_blank"><?php echo esc_html($product['seller_name']); ?></a>
                                                    </div>
                                                </div>
                                            <?php endif;?>
                                            <div class="row product-edit">
                                                <div class="col-md-12 input-block">
                                                    <label><?php _e('Change name', 'bng2woo-lite');?>:</label><input type="text" class="form-control title" maxlength="255" value="<?php echo esc_attr($product['title']); ?>">
                                                </div>
                                                <div>
                                                    <div class="col-md-4 input-block js-choosen-parent">
                                                        <label><?php _e('SKU', 'bng2woo-lite');?>:</label>
                                                        <input type="text" class="form-control sku" maxlength="255" value="<?php echo esc_attr(empty($product['sku']) ? $product['id'] : $product['sku']); ?>"/>
                                                    </div>
                                                    <div class="col-md-4 input-block js-choosen-parent">
                                                        <label><?php _e('Status', 'bng2woo-lite');?>:</label>
                                                        <select class="form-control select2 status" data-placeholder="<?php _e('Choose Status', 'bng2woo-lite');?>">
                                                            <option value="publish" <?php if ($product['product_status'] == "publish"): ?>selected="selected"<?php endif;?>><?php _e('Publish');?></option>
                                                            <option value="draft" <?php if ($product['product_status'] == "draft"): ?>selected="selected"<?php endif;?>><?php _e('Draft');?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 input-block js-choosen-parent">
                                                        <label><?php _e('Type', 'bng2woo-lite');?>:</label>
                                                        <select class="form-control select2 type" data-placeholder="<?php _e('Choose Type', 'bng2woo-lite');?>">
                                                            <option value="simple" <?php if ($product['product_type'] == "simple"): ?>selected="selected"<?php endif;?>><?php _ex('Simple/Variable Product', 'Setting option', 'bng2woo-lite');?></option>
                                                            <option value="external" <?php if ($product['product_type'] == "external"): ?>selected="selected"<?php endif;?>><?php _ex('External/Affiliate Product', 'Setting option', 'bng2woo-lite');?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 input-block js-build-wrapper">
                                                        <div><label><?php _e('Categories', 'bng2woo-lite');?>:</label></div>
                                                        <select class="form-control select2 categories" data-placeholder="<?php _e('Choose Categories', 'bng2woo-lite');?>" multiple="multiple">
                                                            <option></option>
                                                            <?php foreach ($categories as $c): ?>
                                                                <option value="<?php echo esc_attr($c['term_id']); ?>"<?php if (in_array($c['term_id'], $product['categories'])): ?> selected="selected"<?php endif;?>><?php echo esc_html(str_repeat('- ', $c['level'] - 1) . $c['name']); ?></option>
                                                            <?php endforeach;?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 input-block js-choosen-parent">
                                                        <div><label><?php _e('Tags', 'bng2woo-lite');?>:</label></div>
                                                        <select name="tags" class="form-control select2-tags tags" data-placeholder="<?php _e('Enter Tags', 'bng2woo-lite');?>" multiple="multiple">
                                                            <?php foreach ($product['tags'] as $tag): ?>
                                                                <option value="<?php echo esc_attr($tag); ?>" selected="selected"><?php echo esc_html($tag); ?></option>
                                                            <?php endforeach;?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tabs-content" rel="description">
                                    <?php
wp_editor($product['description'], $product['import_id'], array('editor_class' => 'b2wl_description_editor', 'media_buttons' => false, 'editor_height' => 360/* , 'default_editor'=>'html' */));
?>
                                </div>
                                <div class="tabs-content" rel="variants" <?php if (b2wl_check_defined('B2WL_DO_NOT_IMPORT_VARIATIONS')): ?>style="display:none;"<?php endif;?>>
                                    <div id="variants-images-container-<?php echo esc_attr($product['import_id']); ?>" class="variants-wrap">
                                        <div class="variants-actions">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>
                                                        <label for="disable-price-change-<?php echo esc_attr($product['import_id']); ?>"><?php _e('Prevent product price from auto-updating', 'bng2woo-lite');?></label>
                                                        <input class="form-control disable-var-price-change" type="checkbox" id="disable-price-change-<?php echo esc_attr($product['import_id']); ?>" <?php if (isset($product['disable_var_price_change']) && $product['disable_var_price_change']): ?> checked="checked"<?php endif;?>>
                                                        <div class="info-box" data-toggle="tooltip" title="If you choose to prevent product price from auto-updating, it will not be changed regardless of the auto-updating settings in your account, or price changes made by the supplier. You will only be able to change your price manually."></div>
                                                    </td>
                                                    <td>
                                                        <label for="disable-quantity-change-<?php echo esc_attr($product['import_id']); ?>"><?php _e('Prevent product quantity from auto-updating', 'bng2woo-lite');?></label>
                                                        <input class="form-control disable-var-quantity-change" type="checkbox" id="disable-quantity-change-<?php echo esc_attr($product['import_id']); ?>" <?php if (isset($product['disable_var_quantity_change']) && $product['disable_var_quantity_change']): ?> checked="checked"<?php endif;?>>
                                                        <div class="info-box" data-toggle="tooltip" title="If you choose to prevent product quantity from auto-updating, it will not be changed regardless of the auto-updating settings in your account, or quantity changes made by the supplier. You will only be able to change your quantity manually."></div>
                                                    </td>
                                                </tr>
                                            </table>

                                        </div>

                                        <table class="variants-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="nowrap"><input type="checkbox" class="check-all-var form-control" <?php if (!$product['skip_vars']): ?> checked="checked"<?php endif;?>><?php _e('Use all', 'bng2woo-lite');?></th>
                                                    <th><?php _e('SKU', 'bng2woo-lite');?></th>
                                                    <?php foreach ($product['sku_products']['attributes'] as $attr): ?>
                                                        <th>
                                                            <input type="text" class="form-control attr-name" data-id="<?php echo $attr['id']; ?>" value="<?php echo $attr['name']; ?>">
                                                        </th>
                                                    <?php endforeach;?>
                                                    <th><?php _e('Cost', 'bng2woo-lite');?></th>
                                                    
                                                    <th><?php _e('Price', 'bng2woo-lite');?></th>
                                                    <th><?php _e('Regular Price', 'bng2woo-lite');?></th>
                                                    <th><?php _e('Profit', 'bng2woo-lite');?></th>
                                                    <th><?php _e('Inventory', 'bng2woo-lite');?></th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"></td>
                                                    <?php foreach ($product['sku_products']['attributes'] as $attr): ?>
                                                        <td data-attr-id="<?php echo esc_attr($attr['id']); ?>">
                                                            <div class="price-edit-selector edit-price large rename-attr">
                                                                <div class="price-box-top">
                                                                    <div class="container-flex">
                                                                        <div>
                                                                            <div class="slt"><select class="form-control"></select></div>
                                                                        </div>
                                                                        <div>
                                                                            <input type="text" class="form-control" placeholder="New name">
                                                                        </div>
                                                                        <div>
                                                                            <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                        </div>
                                                                        <div>
                                                                            <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <?php _e('Change Attr', 'bng2woo-lite');?> <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-sm">
                                                                    <li><a href="javascript:void(0)" class="rename-attr-value"><?php _e('Rename attributes', 'bng2woo-lite');?></a></li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    <?php endforeach;?>
                                                    <td></td>
                                                    

                                                    <td>
                                                        <div class="price-edit-selector edit-price">
                                                            <div class="price-box-top">
                                                                <div class="container-flex">
                                                                    <div>
                                                                        <input type="text" class="form-control" placeholder="Enter Value">
                                                                    </div>
                                                                    <div>
                                                                        <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?php _e('Change Prices', 'bng2woo-lite');?> <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-sm">
                                                                <li><a href="javascript:void(0)" class="set-new-value"><?php _e('Set New Value', 'bng2woo-lite');?></a></li>
                                                                <li><a href="javascript:void(0)" class="multiply-by-value"><?php _e('Multiply by', 'bng2woo-lite');?></a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="price-edit-selector edit-regular-price">
                                                            <div class="price-box-top">
                                                                <div class="container-flex">
                                                                    <div>
                                                                        <input type="text" class="form-control" placeholder="Enter Value">
                                                                    </div>
                                                                    <div>
                                                                        <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?php _e('Change Prices', 'bng2woo-lite');?> <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-sm">
                                                                <li><a href="javascript:void(0)" class="set-new-value"><?php _e('Set New Value', 'bng2woo-lite');?></a></li>
                                                                <li><a href="javascript:void(0)" class="multiply-by-value"><?php _e('Multiply by', 'bng2woo-lite');?></a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <div class="price-edit-selector edit-quantity">
                                                            <div class="price-box-top price-box-right">
                                                                <div class="container-flex">
                                                                    <div>
                                                                        <input type="text" class="form-control simple-value" placeholder="<?php _e('Enter Value', 'bng2woo-lite');?>">
                                                                        <input type="text" class="form-control random-from" placeholder="<?php _e('From', 'bng2woo-lite');?>">
                                                                        <input type="text" class="form-control random-to" placeholder="<?php _e('To', 'bng2woo-lite');?>">
                                                                    </div>
                                                                    <div>
                                                                        <button class="apply btn btn-default margin-small-left">Apply</button>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="close btn btn-default"><span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-icon-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <?php _e('Change Inv.', 'bng2woo-lite');?> <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-sm dropdown-right">
                                                                <li><a href="javascript:void(0)" class="set-new-quantity"><?php _e('Set New Value', 'bng2woo-lite');?></a></li>
                                                                <li><a href="javascript:void(0)" class="random-value"><?php _e('Random Value', 'bng2woo-lite');?></a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>


                                                <?php foreach ($product['sku_products']['variations'] as $i => $var): ?>
                                                    <tr data-id="<?php echo esc_attr($var['id']); ?>" class="var_data">
                                                        <td>
                                                            <input type="checkbox" value="1" class="check-var form-control" <?php if (!in_array($var['id'], $product['skip_vars'])): ?> checked="checked"<?php endif;?>>
                                                        </td>
                                                        <td>
                                                            <?php if (isset($var['image'])): ?><img class="border-img lazy-in-container" style="max-width: 100px; max-height: 100px; margin: 5px" src="<?php echo esc_url(B2WL()->plugin_url() . '/assets/img/blank_image.png'); ?>" data-id="<?php echo md5($var['image']); ?>" data-original="<?php echo esc_url(isset($product['tmp_edit_images'][md5($var['image'])]['attachment_url']) ? $product['tmp_edit_images'][md5($var['image'])]['attachment_url'] : b2wl_image_url($var['image'])); ?>" data-img="<?php echo esc_url(isset($product['tmp_edit_images'][md5($var['image'])]['attachment_url']) ? $product['tmp_edit_images'][md5($var['image'])]['attachment_url'] : b2wl_image_url($var['image'])); ?>" data-toggle="popover-hover"><?php endif;?>

                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control sku" value="<?php echo esc_attr($var['sku']); ?>">
                                                        </td>
                                                        <?php foreach ($var['attributes'] as $j => $av): ?>
                                                            <td data-attr-id="<?php echo esc_attr(explode(":", $av)[0]); ?>"><input type="text" class="form-control attr" data-id="<?php echo esc_attr($av); ?>" value="<?php echo esc_attr(isset($var['attributes_names'][$j]) ? $var['attributes_names'][$j] : ''); ?>"></td>
                                                        <?php endforeach;?>

                                                        <td style="white-space: nowrap;" class="external-price" data-value="<?php echo esc_attr($var['price']); ?>"><?php echo esc_html($localizator->getLocaleCurr($var['currency'])); ?><?php echo esc_html($var['price']); ?></td>
                                                        
                                                        <td>
                                                            <input type="text" class="form-control price" value="<?php echo esc_attr($var['calc_price']); ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control regular_price" value="<?php echo esc_attr($var['calc_regular_price']); ?>">
                                                        </td>
                                                        <td class="profit"><?php echo esc_html($localizator->getLocaleCurr($var['currency'])); ?><span class="value"><?php echo esc_html(round($var['calc_price'] - $var['price'], 2)); ?></span></td>
                                                        <td><input type="text" class="form-control quantity" value="<?php echo esc_attr($var['quantity']); ?>"></td>
                                                    </tr>
                                                <?php endforeach;?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <script>
                                        (function ($) {
                                            $(".variants-wrap img.lazy-in-container").lazyload({effect: "fadeIn", skip_invisible: true, container: $("#variants-images-container-<?php echo esc_attr($product['import_id']); ?>")});
                                        })(jQuery);
                                    </script>

                                </div>
                                <div class="tabs-content" rel="images">
                                    <div id="images-container-<?php echo esc_attr($product['import_id']); ?>" class="images-wrap">
                                        <?php if (!empty($product['gallery_images'])): ?>
                                            <div class="images-blog-title">
                                                <input type="checkbox" id="check-all-gallery-image-id-<?php echo esc_attr($product['import_id']); ?>" class="check-all-block-image form-control" checked="checked">
                                                <label for="check-all-gallery-image-id-<?php echo esc_attr($product['import_id']); ?>"><?php _e('Gallery images', 'bng2woo-lite');?></label>
                                            </div>
                                            <div class="row gallery_images">
                                                <?php foreach ($product['gallery_images'] as $img_id => $image): ?>
                                                    <div class="col-xs-3">
                                                        <div id="<?php echo esc_attr($img_id); ?>" class="image<?php if (!in_array($img_id, $product['skip_images'])): ?> selected<?php endif;?>">
                                                            <img class="lazy-in-container" src="<?php echo esc_url(B2WL()->plugin_url() . '/assets/img/blank_image.png'); ?>" data-id="<?php echo esc_attr($img_id); ?>" data-original="<?php echo esc_url(isset($product['tmp_edit_images'][$img_id]['attachment_url']) ? $product['tmp_edit_images'][$img_id]['attachment_url'] : b2wl_image_url($image)); ?>"/>
                                                            <div class="icon-selected-box align-center"><svg class="icon-selected"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-selected"></use></svg></div>
                                                            <div class="icon-gallery-box align-center<?php if ($product['thumb_id'] == $img_id): ?> selected<?php endif;?>"><svg class="icon-star"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-star"></use></svg></div>
                                                            <?php if (isset($product['tmp_move_images'][$img_id])): ?>
                                                                <div class="cancel-image-action"><a href="#" data-action="move#<?php echo esc_attr($product['tmp_move_images'][$img_id]); ?>"><?php _e('Cancel move', 'bng2woo-lite');?></a></div>
                                                            <?php elseif (isset($product['tmp_copy_images'][$img_id])): ?>
                                                                <div class="cancel-image-action"><a href="#" data-action="copy#<?php echo esc_attr($product['tmp_copy_images'][$img_id]); ?>"><?php _e('Cancel copy', 'bng2woo-lite');?></a></div>
                                                            <?php endif;?>
                                                        </div>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        <?php endif;?>
                                        <?php if (!empty($product['variant_images'])): ?>
                                            <div class="images-blog-title">
                                                <input type="checkbox" id="check-all-variant-image-id-<?php echo esc_attr($product['import_id']); ?>" class="check-all-block-image form-control" checked="checked">
                                                <label for="check-all-variant-image-id-<?php echo esc_attr($product['import_id']); ?>"><?php _e('Variations images', 'bng2woo-lite');?></label>
                                            </div>
                                            <div class="row variant_images">
                                                <?php foreach ($product['variant_images'] as $img_id => $image): ?>
                                                    <div class="col-xs-3">
                                                        <div id="<?php echo esc_attr($img_id); ?>" class="image<?php if (!in_array($img_id, $product['skip_images'])): ?> selected<?php endif;?>">
                                                            <img class="lazy-in-container" src="<?php echo esc_url(B2WL()->plugin_url() . '/assets/img/blank_image.png'); ?>" data-id="<?php echo esc_attr($img_id); ?>" data-original="<?php echo esc_url(isset($product['tmp_edit_images'][$img_id]['attachment_url']) ? $product['tmp_edit_images'][$img_id]['attachment_url'] : b2wl_image_url($image)); ?>"/>
                                                            <div class="icon-selected-box align-center"><svg class="icon-selected"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-selected"></use></svg></div>
                                                            <div class="icon-gallery-box align-center<?php if ($product['thumb_id'] == $img_id): ?> selected<?php endif;?>"><svg class="icon-star"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-star"></use></svg></div>
                                                        </div>
                                                    </div>
                                                <?php endforeach;?>
                                            </div>
                                        <?php endif;?>
                                        <?php if (!empty($product['description_images'])): ?>
                                            <div class="images-blog-title">
                                                <input type="checkbox" id="check-all-description-image-id-<?php echo esc_attr($product['import_id']); ?>" class="check-all-block-image form-control" checked="checked">
                                                <label for="check-all-description-image-id-<?php echo esc_attr($product['import_id']); ?>"><?php _e('Description images', 'bng2woo-lite');?></label>
                                                <div class="images-action">
                                                    <select class="form-control description-images-action">
                                                        <option value="" disabled="disabled" selected="selected"><?php _e('Action', 'bng2woo-lite');?></option>
                                                        <option value="move"><?php _e('Move checked images to gallery', 'bng2woo-lite');?></option>
                                                        <option value="copy"><?php _e('Copy checked images to gallery', 'bng2woo-lite');?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row description_images">
                                                <?php foreach ($product['description_images'] as $img_id => $image): ?>
                                                    <?php if (!isset($product['tmp_move_images'][$img_id])): ?>
                                                        <div class="col-xs-3">
                                                            <div id="<?php echo esc_attr($img_id); ?>" class="image<?php if (!in_array($img_id, $product['skip_images'])): ?> selected<?php endif;?>">
                                                                <img class="lazy-in-container" src="<?php echo esc_url(B2WL()->plugin_url() . '/assets/img/blank_image.png'); ?>" data-id="<?php echo esc_attr($img_id); ?>" data-original="<?php echo esc_url(isset($product['tmp_edit_images'][$img_id]['attachment_url']) ? $product['tmp_edit_images'][$img_id]['attachment_url'] : b2wl_image_url($image)); ?>"/>
                                                                <div class="icon-selected-box align-center"><svg class="icon-selected"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-selected"></use></svg></div>
                                                                <div class="icon-gallery-box align-center<?php if ($product['thumb_id'] == $img_id): ?> selected<?php endif;?>"><svg class="icon-star"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-star"></use></svg></div>
                                                            </div>
                                                        </div>
                                                    <?php endif;?>
                                                <?php endforeach;?>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                    <script>
                                        (function ($) {
                                            $(".images-wrap img.lazy-in-container").lazyload({effect: "fadeIn", skip_invisible: true, container: $("#images-container-<?php echo esc_attr($product['import_id']); ?>")});
                                        })(jQuery);
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>

            <?php include_once 'includes/confirm_modal.php';?>
            
            <?php include_once 'includes/category_modal.php';?>
            
        </div>


        <?php if ($paginator['total_pages'] > 1): ?>
            <div class="pagination">
                <div class="pagination__wrapper">
                    <ul class="pagination__list">
                        <li <?php if (1 == $paginator['cur_page']): ?>class="disabled"<?php endif;?>><a href="<?php echo admin_url('admin.php?page=b2wl_import&cur_page=' . ($paginator['cur_page'] - 1)) ?>">«</a></li>
                        <?php foreach ($paginator['pages_list'] as $p): ?>
                            <?php if ($p): ?>
                                <?php if ($p == $paginator['cur_page']): ?>
                                    <li class="active"><span><?php echo esc_html($p); ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=b2wl_import&cur_page=' . intval($p))); ?>"><?php echo esc_html($p); ?></a></li>
                                <?php endif;?>
                            <?php else: ?>
                                <li class="disabled"><span>...</span></li>
                            <?php endif;?>
                        <?php endforeach;?>
                        <li <?php if ($paginator['total_pages'] <= $paginator['cur_page']): ?>class="disabled"<?php endif;?>><a href="<?php echo esc_url(admin_url('admin.php?page=b2wl_import&cur_page=' . ($paginator['cur_page'] + 1))); ?>">»</a></li>
                    </ul>
                </div>
            </div>
        <?php endif;?>
    </div>

    <script>
        (function ($) {
            if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }

            $(function () {
                $( ".specs-sortable" ).sortable({handle: ".column-handle"});
                $( ".specs-sortable" ).disableSelection();
            });

            $(".product .select2").select2();
            $('.product .select2-tags').select2({tags: true, tokenSeparators: [','], ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            action: 'b2wl_search_tags',
                            search: params.term,
                            page: params.page || 1
                        }
                        return query;
                    }
                }});
            $('.dropdown-toggle').dropdown();
            $(".set-category-dialog .select2").select2({width: '100%'});

            $('.product .nav-tabs a').click(function () {
                $(this).parents('.product').children("div.tabs-content").removeClass("active");
                $(this).parents('.product').children('div.tabs-content[rel="' + $(this).attr("rel") + '"]').addClass("active");

                $(this).parents('.product').find(".nav-tabs li").removeClass("active");
                $(this).parents('.product').find('.nav-tabs li a[rel="' + $(this).attr("rel") + '"]').closest('li').addClass("active");

                if ($(this).attr("rel") === 'images') {
                    $(this).parents('.product').children('div.tabs-content[rel="' + $(this).attr("rel") + '"]').find('img.lazy-in-container').lazyload();
                }

                if ($(this).attr("rel") === 'variants') {
                    $(this).parents('.product').children('div.tabs-content[rel="' + $(this).attr("rel") + '"]').find('img.lazy-in-container').lazyload();
                    
                }
                return false;
            });
        })(jQuery);
    </script>

</div>


<div class="b2wl-content">
    <div class="page-main">
        <div class="_b2wfo b2wl-info"><div>You are using Lite version of the plugin. If you want to unlock all features and get premium support, purchase the full version of the plugin.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
        <?php include_once B2WL()->plugin_path() . '/view/chrome_notify.php';?>
        

        <form class="search-panel" method="GET" id="b2wl-search-form">
            <input type="hidden" name="page" id="page" value="<?php echo esc_attr(((isset($_GET['page'])) ? $_GET['page'] : '')); ?>" />
            <input type="hidden" name="cur_page" id="cur_page" value="<?php echo esc_attr(((isset($_GET['cur_page'])) ? $_GET['cur_page'] : '')); ?>" />
            <input type="hidden" name="b2wl_search" id="b2wl_search" value="1" />

            <div class="search-panel-header">
                <h3 class="search-panel-title"><?php _e('Search for products', 'bng2woo-lite');?></h3>
                <button class="btn btn-default to-right modal-search-open" type="button"><?php _e('Import product by URL or ID', 'bng2woo-lite');?></button>
            </div>
            <div class="search-panel-body">
                <div class="search-panel-simple">
                    <div class="row">
                        <div class="col-lg-10 col-sm-9">
                            <div class="input-group">
                                <select id="b2wl_category_id" class="form-control" name="b2wl_category_id" aria-invalid="false">
                                    <?php foreach ($categories as $cat): ?>
                                        <option <?php if ($cat['cat_id'] > 0 && $cat['level'] == 0): ?>disabled<?php endif;?> value="<?php echo esc_attr($cat['cat_id']); ?>" <?php if (isset($filter['category_id']) && $filter['category_id'] == $cat['cat_id']): ?>selected="selected"<?php endif;?>><?php echo esc_html(str_repeat("- ", $cat['level'])); ?><?php echo esc_html($cat['cat_name']); ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-3">
                            <div class="search-panel-buttons">
                                <button class="btn btn-info no-outline" id="b2wl-do-filter" type="button"><?php _e('Search', 'bng2woo-lite');?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-overlay modal-search">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title"><?php _e('Import product by URL or ID', 'bng2woo-lite');?></h3>
                        <a class="modal-btn-close" href="#"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></a>
                    </div>
                    <div class="modal-body">
                        <label><?php _e('Product URL', 'bng2woo-lite');?></label>
                        <input class="form-control" type="text" id="url_value">
                        <div class="separator"><?php _e('or', 'bng2woo-lite');?></div>
                        <label><?php _e('Product ID', 'bng2woo-lite');?></label>
                        <input class="form-control" type="text" id="id_value">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default modal-close" type="button"><?php _e('Cancel');?></button>
                        <button id="import-by-id-url-btn" class="btn btn-success" type="button">
                            <div class="btn-icon-wrap cssload-container"><div class="cssload-speeding-wheel"></div></div>
                            <?php _e('Import', 'bng2woo-lite');?>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div>
            <div class="import-all-panel">
                <button type="button" class="btn btn-success no-outline btn-icon-left import_all"><div class="btn-loader-wrap"><div class="e2w-loader"></div></div><span class="btn-icon-wrap add"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-add"></use></svg></span>Add all to import list</button>
            </div>

            <div style="clear: both;"></div>
        </div>

        <div class="search-result">
            <div class="messages"><?php settings_errors('b2wl_products_list');?></div>
            <?php $localizator = B2WL_BanggoodLocalizator::getInstance();?>
            <?php $out_curr = $localizator->getLocaleCurr();?>
            <?php if ($load_products_result['state'] != 'error'): ?>
                <?php if (!$load_products_result['total']): ?>
                    <p style="padding: 0 23px;"><?php _e("Banggood's API returns empty result for the selected category, try to choose other categories.", 'bng2woo-lite');?></p>
                <?php else: ?>
                    <?php $row_ind = 0;?>
                    <?php foreach ($load_products_result['products'] as $product): ?>
                        <?php
if ($row_ind == 0) {
    echo '<div class="search-result__row">';
}
?>
                        <article class="product-card<?php if ($product['import_id']): ?> product-card--added<?php endif;?>" data-id="<?php echo $product['id'] ?>">
                            <div class="product-card__img"><a href="<?php echo esc_url($product['url']) ?>" target="_blank"><img src="<?php echo esc_url(B2WL()->plugin_url() . '/assets/img/blank_image.png'); ?>" class="lazy" data-original="<?php echo esc_attr(!empty($product['img']) ? $product['img'] : ""); ?>" alt="#"></a>
                                <div class="product-card__marked-corner">
                                    <svg class="product-card__marked-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-selected"></use></svg>
                                </div>
                            </div>
                            <div class="product-card__body">
                                <div class="product-card__meta">
                                    <div class="product-card__title"><a href="<?php echo esc_url($product['url']); ?>" target="_blank"><?php echo esc_html($product['product_name']); ?></a></div>
                                </div>
                                <div class="product-card__actions">
                                    <button class="btn <?php echo ($product['import_id']) ? 'btn-default' : 'btn-success'; ?> no-outline btn-icon-left"><span class="title"><?php if ($product['import_id']): ?><?php _e('Remove from import list', 'bng2woo-lite');?><?php else: ?><?php _e('Add to import list', 'bng2woo-lite');?><?php endif;?></span>
                                        <div class="btn-loader-wrap"><div class="b2wl-loader"></div></div>
                                        <span class="btn-icon-wrap add"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-add"></use></svg></span>
                                        <span class="btn-icon-wrap remove"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <?php $row_ind++;?>
                        <?php
if ($row_ind == 4) {
    echo '</div>';
    $row_ind = 0;
}
?>
                    <?php endforeach;?>
                    <?php
if (0 < $row_ind && $row_ind < 4) {
    echo '</div>';
}
?>
                    <?php if (isset($filter['country'])): ?>
                        <script>
                            (function ($) {
                                $(function () {
                                    chech_products_view();
                                    $(window).scroll(function () {
                                        chech_products_view();
                                    });
                                });
                            })(jQuery);
                        </script>
                    <?php endif;?>
                <?php endif;?>
            <?php endif;?>

        </div>
        <?php if ($load_products_result['state'] != 'error' && $load_products_result['total_pages'] > 0): ?>
            <div id="b2wl-search-pagination" class="pagination">
                <div class="pagination__wrapper">
                    <ul class="pagination__list">
                        <li <?php if (1 == $load_products_result['page']): ?>class="disabled"<?php endif;?>><a href="#" rel="<?php echo $load_products_result['page'] - 1; ?>">«</a></li>
                        <?php foreach ($load_products_result['pages_list'] as $p): ?>
                            <?php if ($p): ?>
                                <?php if ($p == $load_products_result['page']): ?>
                                    <li class="active"><span><?php echo esc_html($p); ?></span></li>
                                <?php else: ?>
                                    <li><a href="#" rel="<?php echo esc_attr($p); ?>"><?php echo esc_html($p); ?></a></li>
                                <?php endif;?>
                            <?php else: ?>
                                <li class="disabled"><span>...</span></li>
                            <?php endif;?>
                        <?php endforeach;?>
                        <li <?php if ($load_products_result['total_pages'] == $load_products_result['page']): ?>class="disabled"<?php endif;?>><a href="#" rel="<?php echo esc_attr($load_products_result['page'] + 1); ?>">»</a></li>
                    </ul>
                </div>
            </div>
        <?php endif;?>

        <?php include_once 'includes/confirm_modal.php';?>
    </div>
</div>

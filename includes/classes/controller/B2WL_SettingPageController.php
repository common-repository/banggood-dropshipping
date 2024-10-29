<?php

/**
 * Description of B2WL_SettingPage
 *
 * @author Andrey
 *
 * @autoload: b2wl_admin_init
 */
if (!class_exists('B2WL_SettingPageController')) {

    class B2WL_SettingPageController extends B2WL_AbstractAdminPage
    {

        private $product_import_model;
        private $woocommerce_model;
        private $localizator;

        public function __construct()
        {
            parent::__construct(__('Settings', 'bng2woo-lite'), __('Settings', 'bng2woo-lite'), 'import', 'b2wl_setting', 30);

            $this->product_import_model = new B2WL_ProductImport();
            $this->woocommerce_model = new B2WL_Woocommerce();
            $this->localizator = B2WL_BanggoodLocalizator::getInstance();

            add_action('wp_ajax_b2wl_update_price_rules', array($this, 'ajax_update_price_rules'));

            add_action('wp_ajax_b2wl_apply_pricing_rules', array($this, 'ajax_apply_pricing_rules'));

            add_action('wp_ajax_b2wl_update_phrase_rules', array($this, 'ajax_update_phrase_rules'));

            add_action('wp_ajax_b2wl_apply_phrase_rules', array($this, 'ajax_apply_phrase_rules'));

            add_action('wp_ajax_b2wl_get_status_apply_phrase_rules', array($this, 'ajax_get_status_apply_phrase_rules'));

            add_action('wp_ajax_b2wl_reset_shipping_meta', array($this, 'ajax_reset_shipping_meta'));

            add_action('wp_ajax_b2wl_calc_external_images_count', array($this, 'ajax_calc_external_images_count'));
            add_action('wp_ajax_b2wl_calc_external_images', array($this, 'ajax_calc_external_images'));
            add_action('wp_ajax_b2wl_load_external_image', array($this, 'ajax_load_external_image'));

            add_filter('b2wl_setting_view', array($this, 'setting_view'));

            add_filter('b2wl_configure_lang_data', array($this, 'configure_lang_data'));

        }

        public function configure_lang_data($lang_data)
        {
            if ($this->is_current_page()) {
                $lang_data = array(
                    'process_loading_d_of_d_erros_d' => _x('Process loading %d of %d. Errors: %d.', 'Status', 'bng2woo-lite'),
                    'load_button_text' => _x('Load %d images', 'Status', 'bng2woo-lite'),
                    'all_images_loaded_text' => _x('All images loaded', 'Status', 'bng2woo-lite'),
                );
            }
            return $lang_data;
        }

        public function render($params = array())
        {
            $current_module = isset($_REQUEST['subpage']) ? sanitize_key($_REQUEST['subpage']) : 'common';

            $this->model_put("modules", $this->getModules());
            $this->model_put("current_module", $current_module);

            $this->include_view(array("settings/settings_head.php", apply_filters('b2wl_setting_view', $current_module), "settings/settings_footer.php"));
        }

        public function getModules()
        {
            return apply_filters('b2wl_setting_modules', array(
                array('id' => 'common', 'name' => __('Common settings', 'bng2woo-lite')),
                array('id' => 'account', 'name' => __('Account settings', 'bng2woo-lite')),
                array('id' => 'price_formula', 'name' => __('Pricing Rules', 'bng2woo-lite')),
                array('id' => 'shipping', 'name' => __('Shipping settings', 'bng2woo-lite')),
                array('id' => 'phrase_filter', 'name' => __('Phrase Filtering', 'bng2woo-lite')),
                array('id' => 'chrome_api', 'name' => __('API Keys', 'bng2woo-lite')),
                array('id' => 'system_info', 'name' => __('System Info', 'bng2woo-lite')),
            ));
        }

        public function setting_view($current_module)
        {
            $view = "";
            switch ($current_module) {
                case 'common':
                    $view = $this->common_handle();
                    break;
                case 'account':
                    $view = $this->account_handle();
                    break;
                case 'price_formula':
                    $view = $this->price_formula();
                    break;
                case 'shipping':
                    $view = $this->shipping();
                    break;
                case 'phrase_filter':
                    $view = $this->phrase_filter();
                    break;
                case 'chrome_api':
                    $view = $this->chrome_api();
                    break;
                case 'system_info':
                    $view = $this->system_info();
                    break;
            }
            return $view;
        }

        private function common_handle()
        {
            global $b2wl_settings;
            if (isset($_POST['setting_form'])) {
                b2wl_settings()->auto_commit(false);

                b2wl_set_setting('item_purchase_code', isset($_POST['b2wl_item_purchase_code']) ? wp_unslash($_POST['b2wl_item_purchase_code']) : '');

                b2wl_set_setting('import_language', isset($_POST['b2w_import_language']) ? wp_unslash(sanitize_text_field($_POST['b2w_import_language'])) : 'en');
                b2wl_set_setting('local_currency', isset($_POST['b2w_local_currency']) ? wp_unslash(sanitize_text_field($_POST['b2w_local_currency'])) : 'USD');
                b2wl_set_setting('default_product_type', isset($_POST['b2wl_default_product_type']) ? wp_unslash(sanitize_text_field($_POST['b2wl_default_product_type'])) : 'simple');
                b2wl_set_setting('default_product_status', isset($_POST['b2wl_default_product_status']) ? wp_unslash(sanitize_text_field($_POST['b2wl_default_product_status'])) : 'publish');

                b2wl_set_setting('currency_conversion_factor', isset($_POST['b2wl_currency_conversion_factor']) ? wp_unslash(sanitize_text_field($_POST['b2wl_currency_conversion_factor'])) : '1');
                b2wl_set_setting('import_product_images_limit', isset($_POST['b2wl_import_product_images_limit']) && intval($_POST['b2wl_import_product_images_limit']) ? intval($_POST['b2wl_import_product_images_limit']) : '');
                b2wl_set_setting('import_extended_attribute', isset($_POST['b2wl_import_extended_attribute']) ? 1 : 0);

                b2wl_set_setting('background_import', isset($_POST['b2wl_background_import']) ? 1 : 0);
                b2wl_set_setting('convert_attr_case', isset($_POST['b2wl_convert_attr_case']) ? wp_unslash(sanitize_text_field($_POST['b2wl_convert_attr_case'])) : 'original');
                
                b2wl_set_setting('use_external_image_urls', isset($_POST['b2wl_use_external_image_urls']));
                b2wl_set_setting('not_import_description', isset($_POST['b2wl_not_import_description']));

                $default_stock = !empty($_POST['b2wl_default_stock']) && $_POST['b2wl_default_stock'] >= 0 ? intval($_POST['b2wl_default_stock']) : 0;
                b2wl_set_setting('default_stock', $default_stock);

                b2wl_set_setting('use_random_stock', isset($_POST['b2wl_use_random_stock']));
                if (isset($_POST['b2wl_use_random_stock'])) {
                    $min_stock = (!empty($_POST['b2wl_use_random_stock_min']) && intval($_POST['b2wl_use_random_stock_min']) > 0) ? intval($_POST['b2wl_use_random_stock_min']) : 1;
                    $max_stock = (!empty($_POST['b2wl_use_random_stock_max']) && intval($_POST['b2wl_use_random_stock_max']) > 0) ? intval($_POST['b2wl_use_random_stock_max']) : 1;

                    if ($min_stock > $max_stock) {
                        $min_stock = $min_stock + $max_stock;
                        $max_stock = $min_stock - $max_stock;
                        $min_stock = $min_stock - $max_stock;
                    }
                    b2wl_set_setting('use_random_stock_min', $min_stock);
                    b2wl_set_setting('use_random_stock_max', $max_stock);
                }
                
                b2wl_settings()->commit();
                b2wl_settings()->auto_commit(true);
            }

            $countryModel = new B2WL_Country();

            $this->model_put("currencies", $this->localizator->getCurrencies(false));
            $this->model_put("custom_currencies", $this->localizator->getCurrencies(true));

            $this->model_put("shipping_countries", $countryModel->get_countries());

            $this->model_put("order_statuses", function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : array());

            return "settings/common.php";
        }

        private function account_handle()
        {
            $account = B2WL_Account::getInstance();

            if (isset($_POST['setting_form'])) {

                if (!b2wl_check_defined('B2WL_DEMO_MODE')) {
                    $account->save_banggood_account(isset($_POST['b2wl_appkey']) ? sanitize_text_field($_POST['b2wl_appkey']) : '', isset($_POST['b2wl_secretkey']) ? sanitize_text_field($_POST['b2wl_secretkey']) : '', isset($_POST['b2wl_trackingid']) ? sanitize_text_field($_POST['b2wl_trackingid']) : '');
                }
                
            }

            $this->model_put("account", $account);

            return "settings/account.php";
        }

        private function price_formula()
        {
            $formulas = B2WL_PriceFormula::load_formulas();

            if ($formulas) {
                $add_formula = new B2WL_PriceFormula();
                $add_formula->min_price = floatval($formulas[count($formulas) - 1]->max_price) + 0.01;
                $formulas[] = $add_formula;
                $this->model_put("formulas", $formulas);
            } else {

                $this->model_put("formulas", B2WL_PriceFormula::get_default_formulas());
            }

            $this->model_put("pricing_rules_types", B2WL_PriceFormula::pricing_rules_types());

            $this->model_put("default_formula", B2WL_PriceFormula::get_default_formula());

            $this->model_put('cents', b2wl_get_setting('price_cents'));
            $this->model_put('compared_cents', b2wl_get_setting('price_compared_cents'));

            return "settings/price_formula.php";
        }

        private function shipping()
        {
            if (isset($_POST['setting_form'])) {

                b2wl_set_setting('aliship_shipto', isset($_POST['b2w_aliship_shipto']) ? wp_unslash($_POST['b2w_aliship_shipto']) : 'US');
                b2wl_set_setting('default_shipping_class', !empty($_POST['b2wl_default_shipping_class']) ? $_POST['b2wl_default_shipping_class'] : false);
                

            }

            $countryModel = new B2WL_Country();

            $this->model_put("shipping_countries", $countryModel->get_countries());
            
            $shipping_class = get_terms(array('taxonomy' => 'product_shipping_class', 'hide_empty' => false));
            $this->model_put("shipping_class", $shipping_class ? $shipping_class : array());

            return "settings/shipping.php";
        }

        private function phrase_filter()
        {
            $phrases = B2WL_PhraseFilter::load_phrases();

            if ($phrases) {
                $this->model_put("phrases", $phrases);
            } else {
                $this->model_put("phrases", array());
            }

            return "settings/phrase_filter.php";
        }

        private function chrome_api()
        {
            $api_keys = b2wl_get_setting('api_keys', array());

            if (!empty($_REQUEST['delete-key'])) {
                foreach ($api_keys as $k => $key) {
                    if ($key['id'] === $_REQUEST['delete-key']) {
                        unset($api_keys[$k]);
                        b2wl_set_setting('api_keys', $api_keys);
                        break;
                    }
                }
                wp_redirect(admin_url('admin.php?page=b2wl_setting&subpage=chrome_api'));
            } else if (!empty($_POST['b2wl_api_key'])) {
                $key_id = $_POST['b2wl_api_key'];
                $key_name = !empty($_POST['b2wl_api_key_name']) ? $_POST['b2wl_api_key_name'] : "New key";

                $is_new = true;
                foreach ($api_keys as &$key) {
                    if ($key['id'] === $key_id) {
                        $key['name'] = $key_name;
                        $is_new = false;
                        break;
                    }
                }

                if ($is_new) {
                    $api_keys[] = array('id' => $key_id, 'name' => $key_name);
                }

                b2wl_set_setting('api_keys', $api_keys);

                wp_redirect(admin_url('admin.php?page=b2wl_setting&subpage=chrome_api&edit-key=' . $key_id));
            } else if (isset($_REQUEST['edit-key'])) {
                $api_key = array('id' => md5("b2wkey" . rand() . microtime()), 'name' => "New key");
                $is_new = true;
                if (empty($_REQUEST['edit-key'])) {
                    $api_keys[] = $api_key;
                    b2wl_set_setting('api_keys', $api_keys);

                    wp_redirect(admin_url('admin.php?page=b2wl_setting&subpage=chrome_api&edit-key=' . $api_key['id']));
                } else if (!empty($_REQUEST['edit-key']) && $api_keys && is_array($api_keys)) {
                    foreach ($api_keys as $key) {
                        if ($key['id'] === $_REQUEST['edit-key']) {
                            $api_key = $key;
                            $is_new = false;
                        }
                    }
                }
                $this->model_put("api_key", $api_key);
                $this->model_put("is_new_api_key", $is_new);
            }

            $this->model_put("api_keys", $api_keys);

            return "settings/chrome.php";
        }

        private function system_info()
        {
            if (isset($_POST['setting_form'])) {
                b2wl_set_setting('write_info_log', isset($_POST['b2wl_write_info_log']));
            }

            $server_ip = '-';
            if (array_key_exists('SERVER_ADDR', $_SERVER)) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } elseif (array_key_exists('LOCAL_ADDR', $_SERVER)) {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            } elseif (array_key_exists('SERVER_NAME', $_SERVER)) {
                $server_ip = gethostbyname($_SERVER['SERVER_NAME']);
            }

            $this->model_put("server_ip", $server_ip);

            return "settings/system_info.php";
        }

        public function ajax_update_phrase_rules()
        {
            b2wl_init_error_handler();

            $result = B2WL_ResultBuilder::buildOk();
            try {

                B2WL_PhraseFilter::deleteAll();

                if (isset($_POST['phrases'])) {
                    foreach ($_POST['phrases'] as $phrase) {
                        $filter = new B2WL_PhraseFilter(array_map('sanitize_text_field', $phrase));
                        $filter->save();
                    }
                }

                $result = B2WL_ResultBuilder::buildOk(array('phrases' => B2WL_PhraseFilter::load_phrases()));

                restore_error_handler();
            } catch (Throwable $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            }

            echo json_encode($result);

            wp_die();
        }

        public function ajax_apply_phrase_rules()
        {
            b2wl_init_error_handler();

            $result = B2WL_ResultBuilder::buildOk();
            try {

                $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : false;
                $scope = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : false;

                if ($type === 'products' || $type === 'all_types') {
                    if ($scope === 'all' || $scope === 'import') {
                        $products = $this->product_import_model->get_product_list(false);

                        foreach ($products as $product) {

                            $product = B2WL_PhraseFilter::apply_filter_to_product($product);
                            $this->product_import_model->upd_product($product);
                        }
                    }

                    if ($scope === 'all' || $scope === 'shop') {
                        //todo: update attributes as well
                        B2WL_PhraseFilter::apply_filter_to_products();
                    }
                }
                restore_error_handler();
            } catch (Throwable $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            }

            echo json_encode($result);

            wp_die();
        }

        public function ajax_update_price_rules()
        {
            b2wl_init_error_handler();

            $result = B2WL_ResultBuilder::buildOk();
            try {
                b2wl_settings()->auto_commit(false);

                $use_extended_price_markup = isset($_POST['use_extended_price_markup']) ? filter_var($_POST['use_extended_price_markup'], FILTER_VALIDATE_BOOLEAN) : false;
                $use_compared_price_markup = isset($_POST['use_compared_price_markup']) ? filter_var($_POST['use_compared_price_markup'], FILTER_VALIDATE_BOOLEAN) : false;

                b2wl_set_setting('price_cents', isset($_POST['cents']) && intval($_POST['cents']) > -1 && intval($_POST['cents']) <= 99 ? intval(wp_unslash($_POST['cents'])) : -1);
                if ($use_compared_price_markup) {
                    b2wl_set_setting('price_compared_cents', isset($_POST['compared_cents']) && intval($_POST['compared_cents']) > -1 && intval($_POST['compared_cents']) <= 99 ? intval(wp_unslash($_POST['compared_cents'])) : -1);
                } else {
                    b2wl_set_setting('price_compared_cents', -1);
                }

                b2wl_set_setting('use_extended_price_markup', $use_extended_price_markup);
                b2wl_set_setting('use_compared_price_markup', $use_compared_price_markup);

                b2wl_set_setting('add_shipping_to_price', !empty($_POST['add_shipping_to_price']) ? filter_var($_POST['add_shipping_to_price'], FILTER_VALIDATE_BOOLEAN) : false);
                b2wl_set_setting('apply_price_rules_after_shipping_cost', !empty($_POST['apply_price_rules_after_shipping_cost']) ? filter_var($_POST['apply_price_rules_after_shipping_cost'], FILTER_VALIDATE_BOOLEAN) : false);

                b2wl_settings()->commit();
                b2wl_settings()->auto_commit(true);

                if (isset($_POST['rules'])) {
                    B2WL_PriceFormula::deleteAll();
                    foreach ($_POST['rules'] as $rule) {
                        $formula = new B2WL_PriceFormula($rule);
                        $formula->save();
                    }
                }

                if (isset($_POST['default_rule'])) {

                    B2WL_PriceFormula::set_default_formula(new B2WL_PriceFormula(array_map('sanitize_text_field', $_POST['default_rule'])));
                }

                $result = B2WL_ResultBuilder::buildOk(array('rules' => B2WL_PriceFormula::load_formulas(), 'default_rule' => B2WL_PriceFormula::get_default_formula(), 'use_extended_price_markup' => $use_extended_price_markup, 'use_compared_price_markup' => $use_compared_price_markup));

                restore_error_handler();
            } catch (Throwable $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            }

            echo json_encode($result);

            wp_die();
        }

        public function ajax_apply_pricing_rules()
        {
            b2wl_init_error_handler();

            $result = B2WL_ResultBuilder::buildOk(array('done' => 1));
            try {

                $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : false;
                $scope = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : false;
                $page = isset($_POST['page']) ? intval($_POST['page']) : 0;
                $import_page = isset($_POST['import_page']) ? intval($_POST['import_page']) : 0;

                if ($page == 0 && ($scope === 'all' || $scope === 'import')) {
                    $products_count = $this->product_import_model->get_products_count();

                    $update_per_request = b2wl_check_defined('B2WL_UPDATE_PRODUCT_IN_IMPORTLIST_PER_REQUEST');
                    $update_per_request = $update_per_request ? B2WL_UPDATE_PRODUCT_IN_IMPORTLIST_PER_REQUEST : 50;

                    $products_id_list = $this->product_import_model->get_product_id_list($update_per_request, $update_per_request * $import_page);
                    foreach ($products_id_list as $product_id) {
                        $product = $this->product_import_model->get_product($product_id);
                        if (!isset($product['disable_var_price_change']) || !$product['disable_var_price_change']) {
                            $product = B2WL_PriceFormula::apply_formula($product, 2, $type);
                            $this->product_import_model->upd_product($product);
                        }
                        unset($product);
                    }
                    unset($products_id_list);

                    if (($import_page * $update_per_request + $update_per_request) >= $products_count) {
                        $result = B2WL_ResultBuilder::buildOk(array('done' => 1, 'info' => 'Import: 100%'));
                    } else {
                        $result = B2WL_ResultBuilder::buildOk(array('done' => 0, 'info' => 'Import: ' . round(100 * ($import_page * $update_per_request + $update_per_request) / $products_count, 2) . '%'));
                    }
                }
                if ($result['done'] == 1 && ($scope === 'all' || $scope === 'shop')) {

                    $update_per_request = b2wl_check_defined('B2WL_UPDATE_PRODUCT_PER_REQUEST');
                    $update_per_request = $update_per_request ? B2WL_UPDATE_PRODUCT_PER_REQUEST : 30;

                    $products_count = $this->woocommerce_model->get_products_count();
                    if (($page * $update_per_request + $update_per_request) >= $products_count) {
                        $result = B2WL_ResultBuilder::buildOk(array('done' => 1, 'info' => 'Shop: 100%'));
                    } else {
                        $result = B2WL_ResultBuilder::buildOk(array('done' => 0, 'info' => 'Shop: ' . round(100 * ($page * $update_per_request + $update_per_request) / $products_count, 2) . '%'));
                    }

                    $product_ids = $this->woocommerce_model->get_products_ids($page, $update_per_request);
                    foreach ($product_ids as $product_id) {
                        $product = $this->woocommerce_model->get_product_by_post_id($product_id);

                        if (!isset($product['disable_var_price_change']) || !$product['disable_var_price_change']) {
                            $product = B2WL_PriceFormula::apply_formula($product, 2, $type);
                            if (isset($product['sku_products']['variations']) && count($product['sku_products']['variations']) > 0) {

                                $this->woocommerce_model->update_price($product_id, $product['sku_products']['variations'][0]);
                                foreach ($product['sku_products']['variations'] as $var) {
                                    $variation_id = get_posts(array('post_type' => 'product_variation', 'fields' => 'ids', 'numberposts' => 100, 'post_parent' => $product_id, 'meta_query' => array(array('key' => 'external_variation_id', 'value' => $var['id']))));
                                    $variation_id = $variation_id ? $variation_id[0] : false;
                                    if ($variation_id) {
                                        $this->woocommerce_model->update_price($variation_id, $var);
                                    }
                                }
                                wc_delete_product_transients($product_id);
                            }
                        }
                        unset($product);
                    }
                    unset($product_ids);
                }

                restore_error_handler();
            } catch (Throwable $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            } catch (Exception $e) {
                b2wl_print_throwable($e);
                $result = B2WL_ResultBuilder::buildError($e->getMessage());
            }

            echo json_encode($result);

            wp_die();
        }

        public function ajax_calc_external_images_count()
        {
            echo json_encode(B2WL_ResultBuilder::buildOk(array('total_images' => B2WL_Attachment::calc_total_external_images())));
            wp_die();
        }

        public function ajax_calc_external_images()
        {
            $page_size = isset($_POST['page_size']) && intval($_POST['page_size']) > 0 ? intval($_POST['page_size']) : 1000;
            $result = B2WL_ResultBuilder::buildOk(array('ids' => B2WL_Attachment::find_external_images($page_size)));
            echo json_encode($result);
            wp_die();
        }

        public function ajax_load_external_image()
        {
            global $wpdb;

            b2wl_init_error_handler();

            $attachment_model = new B2WL_Attachment('local');

            $image_id = isset($_POST['id']) && intval($_POST['id']) > 0 ? intval($_POST['id']) : 0;

            if ($image_id) {
                try {
                    $attachment_model->load_external_image($image_id);

                    $result = B2WL_ResultBuilder::buildOk();
                } catch (Throwable $e) {
                    b2wl_print_throwable($e);
                    $result = B2WL_ResultBuilder::buildError($e->getMessage());
                } catch (Exception $e) {
                    b2wl_print_throwable($e);
                    $result = B2WL_ResultBuilder::buildError($e->getMessage());
                }
            } else {
                $result = B2WL_ResultBuilder::buildError("load_external_image: waiting for ID...");
            }

            echo json_encode($result);
            wp_die();
        }

        public function ajax_reset_shipping_meta()
        {
            $result = B2WL_ResultBuilder::buildOk();
            //remove saved shipping meta
            B2WL_ProductShippingMeta::clear_in_all_product();
            echo json_encode($result);
            wp_die();
        }

    }

}

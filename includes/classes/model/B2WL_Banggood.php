<?php

/**
 * Description of B2WL_Banggood
 *
 * @author Andrey
 */

if (!class_exists('B2WL_Banggood')) {

    class B2WL_Banggood
    {

        private $product_import_model;
        private $account;
        public static $shipfrom_attribute = "warehouse";

        public function __construct()
        {
            $this->product_import_model = new B2WL_ProductImport();
            $this->account = B2WL_Account::getInstance();
        }

        public function load_categories()
        {
            /** @var wpdb $wpdb */
            global $wpdb;

            $page = 1;
            $result = B2WL_ResultBuilder::buildOk(array('categories' => array()));
            while (true) {
                $request = B2WL_RequestHelper::build_request('get_categories', array('page' => $page));
                if ($request['state'] === 'error') {
                    return $request;
                }

                $request = b2wl_remote_get($request['request_url']);
                if (is_wp_error($request)) {
                    return B2WL_ResultBuilder::buildError($request->get_error_message());
                } else if (intval($request['response']['code']) != 200) {
                    return B2WL_ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
                } else {
                    $body = json_decode($request['body'], true);

                    if (intval($body['code']) > 0) {
                        return B2WL_ResultBuilder::buildError($body['code'] . " " . $body['msg']);
                    }

                    $result['categories'] = array_merge($result['categories'], $body['cat_list']);

                    if ($page < intval($body['page_total'])) {
                        $page++;
                    } else {
                        break;
                    }
                }
            }

            return $result;
        }

        public static function error_mapping($code, $message)
        {
            if ($code == 11020) {
                return "Incorrect Appid or AppSecret in the plugin settings";
            } else if ($code == 31010) {
                return "Failed to get access token: Illegal IP address";
            } else {
                return $code . " " . $message;
            }
        }

        public function load_products($filter, $page = 1, $per_page = 20, $params = array())
        {
            /** @var wpdb $wpdb */
            global $wpdb;

            $products_in_import = $this->product_import_model->get_product_id_list();

            $request = B2WL_RequestHelper::build_request('get_products', array_merge(array('page' => $page), $filter));
            if ($request['state'] === 'error') {
                return $request;
            }

            $dpu = $request['dpu'];

            $request = b2wl_remote_get($request['request_url']);

            if (is_wp_error($request)) {
                $result = B2WL_ResultBuilder::buildError($request->get_error_message());
            } else if (intval($request['response']['code']) != 200) {
                $result = B2WL_ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
            } else {
                $body = json_decode($request['body'], true);

                if (intval($body['code']) > 0) {
                    $result = B2WL_ResultBuilder::buildError(B2WL_Banggood::error_mapping($body['code'], $body['msg']));
                } else {

                    $default_type = b2wl_get_setting('default_product_type');
                    $default_status = b2wl_get_setting('default_product_status');

                    $tmp_urls = array();

                    $products = $body['product_list'];
                    foreach ($products as &$product) {
                        $product['id'] = $product['product_id'];
                        unset($product['product_id']);
                        $product['post_id'] = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_b2w_external_id' AND meta_value='%s' LIMIT 1", $product['id']));
                        $product['import_id'] = in_array($product['id'], $products_in_import) ? $product['id'] : 0;
                        $product['product_type'] = $default_type;
                        $product['product_status'] = $default_status;
                        $product['url'] = "https://www.banggood.com/" . str_replace(" ", "-", $product['product_name']) . "-p-" . $product['id'] . ".html";

                        $tmp_urls[] = $product['url'];
                    }

                    try {
                        $promotionUrls = $this->get_affiliate_urls($tmp_urls, $dpu);
                        if (!empty($promotionUrls) && is_array($promotionUrls)) {
                            foreach ($products as $i => $product) {
                                foreach ($promotionUrls as $pu) {
                                    if ($pu['url'] == $product['url']) {
                                        $products[$i]['affiliate_url'] = $pu['promotionUrl'];
                                        break;
                                    }
                                }
                            }
                        }
                    } catch (Throwable $e) {
                        b2wl_print_throwable($e);
                        foreach ($products as &$product) {
                            $product['affiliate_url'] = $product['url'];
                        }
                    } catch (Exception $e) {
                        b2wl_print_throwable($e);
                        foreach ($products as &$product) {
                            $product['affiliate_url'] = $product['url'];
                        }
                    }

                    $result = B2WL_ResultBuilder::buildOK(
                        array('page' => $body['page'],
                            'page_size' => $body['page_size'],
                            'page_total' => $body['page_size'],
                            'total' => $body['product_total'],
                            'products' => $products,
                        )
                    );
                }
            }
            return $result;
        }

        public function load_product($product_id, $params = array())
        {
            $request = B2WL_RequestHelper::build_request('get_product', array('product_id' => $product_id));
            if ($request['state'] === 'error') {
                return $request;
            }

            $dpu = $request['dpu'];

            $request = b2wl_remote_get($request['request_url']);
            if (is_wp_error($request)) {
                return B2WL_ResultBuilder::buildError($request->get_error_message());
            }

            $product_data = json_decode($request['body'], true);
            if (intval($product_data['code']) > 0) {
                return B2WL_ResultBuilder::buildError(B2WL_Banggood::error_mapping($product_data['code'], $product_data['msg']));
            }

            return $this->process_product($product_id, $product_data, array_merge($params, array('dpu' => $dpu)));
        }

        

        public function load_product_stock($product_id, $params = array())
        {
            $request = B2WL_RequestHelper::build_request('get_product_stock', array('product_id' => $product_id));
            if ($request['state'] === 'error') {
                return $request;
            }

            $request = b2wl_remote_get($request['request_url']);
            if (is_wp_error($request)) {
                return B2WL_ResultBuilder::buildError($request->get_error_message());
            }
            $product_stock_data = json_decode($request['body'], true);
            if (intval($product_stock_data['code']) > 0) {
                return B2WL_ResultBuilder::buildError(B2WL_Banggood::error_mapping($product_stock_data['code'], $product_stock_data['msg']));
            }

            $product_stock = array(
                'product_id' => $product_id,
                /*'stocks_debug'=>$product_stock_data['stocks']*/
            );

            foreach ($product_stock_data['stocks'] as $wh) {

                $wh['warehouse'] = strtolower($wh['warehouse']);

                $product_stock['stocks'][] = array();
                foreach ($wh['stock_list'] as $attribute) {

                    $attr_id = trim($attribute['poa_id']);

                    /**
                     * Banggood sends attributes separated by comma or emty key if only one attribute/variation in the product
                     * we return empty array if no key
                     */
                    if (!empty($attr_id)) {
                        $array_attr_id = explode(',', $attr_id);

                        if (!is_array($array_attr_id)) {
                            $array_attr_id = array($array_attr_id);
                        }

                    } else {
                        $array_attr_id = array();
                    }

                    $product_stock['stocks'][$wh['warehouse']][] = array('code' => $attribute['poa'], 'stock' => $attribute['stock'], 'stock_msg' => $attribute['stock_msg'], 'attribute_values' => $array_attr_id);
                }
            }

            return B2WL_ResultBuilder::buildOk(array('product_stock' => $product_stock));

        }

        public function process_product($product_id, $product_data, $params = array())
        {
            /** @var wpdb $wpdb */
            global $wpdb;

            $products_in_import = $this->product_import_model->get_product_id_list();
            // TODO need to pass lang (for sync)

            $dpu = isset($params['dpu']) ? $params['dpu'] : '';

            $product = array(
                'id' => $product_id,
                'description' => '',
                'currency' => $product_data['currency'],
                'lang' => $product_data['lang'],
                'title' => $product_data['product_name'],
                'weight' => $product_data['weight'],
                'warehouse_list' => $product_data['warehouse_list'],
                'warehouse' => strtolower($product_data['warehouse_list'][0]['warehouse']),
                'images' => is_array($product_data['image_list']['large']) ? array_values($product_data['image_list']['large']) : array($product_data['image_list']['large']),
                'thumb' => '',
                'sku_products' => array('attributes' => array(), 'variations' => array()),
            );

            $default_type = b2wl_get_setting('default_product_type');
            $default_status = b2wl_get_setting('default_product_status');

            $product['product_type'] = $default_type;
            $product['product_status'] = $default_status;
            $product['url'] = "https://www.banggood.com/" . str_replace(" ", "-", $product_data['product_name']) . "-p-" . $product['id'] . ".html";

            try {
                $promotionUrls = $this->get_affiliate_urls($product['url'], $dpu);
                if (!empty($promotionUrls) && is_array($promotionUrls)) {
                    $product['affiliate_url'] = $promotionUrls[0]['promotionUrl'];
                }
            } catch (Throwable $e) {
                b2wl_print_throwable($e);
                $product['affiliate_url'] = $product['url'];
            } catch (Exception $e) {
                b2wl_print_throwable($e);
                $product['affiliate_url'] = $product['url'];
            }

            if (count($product['images']) > 0) {
                $product['thumb'] = $product['images'][0];
            }
            $product['price'] = $product_data['warehouse_list'][0]['warehouse_price'];
            $product['regular_price'] = $product_data['warehouse_list'][0]['warehouse_price'];

            foreach ($product_data['poa_list'] as $attr) {
                $attribute = array('id' => $attr['option_id'], 'name' => $attr['option_name'], 'value' => array());

                foreach ($attr['option_values'] as $val) {
                    $value = array('id' => $attribute['id'] . ':' . $val['poa_id'], 'code' => $val['poa'], 'name' => $val['poa_name'], 'price' => $val['poa_price']);
                    if (isset($val['large_image'])) {
                        $value['image'] = $val['large_image'];
                    }
                    $attribute['value'][$value['id']] = $value;
                }

                $product['sku_products']['attributes'][] = $attribute;
            }

            //add "ship from" attribute
            if (!empty($product['warehouse_list'])) {
                $attribute = array('id' => self::$shipfrom_attribute, 'name' => esc_html__('Ship From', 'bng2woo-lite'), 'value' => array());

                foreach ($product['warehouse_list'] as $wh) {
                    //since this is the "ship_from" atribute, threfore we use 'warehouse' key
                    //instead of 'code' key that we add for other attributes
                    $value = array('id' => $attribute['id'] . ':' . strtolower($wh['warehouse']), 'warehouse' => strtolower($wh['warehouse']), 'name' => $wh['warehouse'], 'price' => $wh['warehouse_price']);
                    $attribute['value'][$value['id']] = $value;

                }
                //make sure "ship from" attribute has the first position
                //because add_product_variations() relies on this
                $product['sku_products']['attributes'] = array_merge(array($attribute), $product['sku_products']['attributes']);
            }

            if (!b2wl_get_setting('not_import_description')) {
                $product['description'] = $this->clean_description($product_data['description']);
                $product['description'] = B2WL_PhraseFilter::apply_filter_to_text($product['description']);
            }

            //load stocks

            $product_stock_data = $this->load_product_stock($product_id);

            if ($product_stock_data['state'] !== 'error') {
                $product_stock = $product_stock_data['product_stock']['stocks'];
            } else {
                b2wl_error_log($product_stock_data['message']);
                $product_stock = array();
            }

            // add variants with prices and stock
            $product = $this->add_product_variations($product, $product_stock);

            if (b2wl_get_setting('use_random_stock')) {
                $product['disable_var_quantity_change'] = true;
                foreach ($product['sku_products']['variations'] as &$variation) {
                    $variation['original_quantity'] = intval($variation['quantity']);
                    $tmp_quantity = rand(intval(b2wl_get_setting('use_random_stock_min')), intval(b2wl_get_setting('use_random_stock_max')));
                    $tmp_quantity = ($tmp_quantity > $variation['original_quantity']) ? $variation['original_quantity'] : $tmp_quantity;
                    $variation['quantity'] = $tmp_quantity;
                }
            }

            if (($convert_attr_casea = b2wl_get_setting('convert_attr_case')) != 'original') {
                $convert_func = false;
                switch ($convert_attr_casea) {
                    case 'lower':
                        $convert_func = function ($v) {return strtolower($v);};
                        break;
                    case 'sentence':
                        $convert_func = function ($v) {return ucfirst(strtolower($v));};
                        break;
                }

                if ($convert_func) {
                    foreach ($product['sku_products']['attributes'] as &$product_attr) {
                        if (!isset($product_attr['original_name'])) {
                            $product_attr['original_name'] = $product_attr['name'];
                        }

                        $product_attr['name'] = $convert_func($product_attr['name']);

                        foreach ($product_attr['value'] as &$product_attr_val) {
                            $product_attr_val['name'] = $convert_func($product_attr_val['name']);
                        }
                    }

                    foreach ($product['sku_products']['variations'] as &$product_var) {
                        $product_var['attributes_names'] = array_map($convert_func, $product_var['attributes_names']);
                    }
                }
            }

            $product['post_id'] = !empty($params['post_id']) ? $params['post_id'] : $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_b2w_external_id' AND meta_value='%s' LIMIT 1", $product['id']));
            $product['import_id'] = in_array($product['id'], $products_in_import) ? $product['id'] : 0;
            $product['import_lang'] = B2WL_BanggoodLocalizator::getInstance()->language;
            
            if (($convert_attr_casea = b2wl_get_setting('convert_attr_case')) != 'original') {
                $convert_func = false;
                switch ($convert_attr_casea) {
                    case 'lower':
                        $convert_func = function ($v) {return strtolower($v);};
                        break;
                    case 'sentence':
                        $convert_func = function ($v) {return ucfirst(strtolower($v));};
                        break;
                }

                if ($convert_func) {
                    foreach ($product['sku_products']['attributes'] as &$product_attr) {
                        if (!isset($product_attr['original_name'])) {
                            $product_attr['original_name'] = $product_attr['name'];
                        }

                        $product_attr['name'] = $convert_func($product_attr['name']);

                        foreach ($product_attr['value'] as &$product_attr_val) {
                            $product_attr_val['name'] = $convert_func($product_attr_val['name']);
                        }
                    }

                    foreach ($product['sku_products']['variations'] as &$product_var) {
                        $product_var['attributes_names'] = array_map($convert_func, $product_var['attributes_names']);
                    }
                }
            }

            $tmp_all_images = B2WL_Utils::get_all_images_from_product($product);

            $not_import_gallery_images = false;
            $not_import_variant_images = false;
            $not_import_description_images = b2wl_get_setting('not_import_description_images');

            $product['skip_images'] = array();
            foreach ($tmp_all_images as $img_id => $img) {
                if (!in_array($img_id, $product['skip_images']) && (($not_import_gallery_images && $img['type'] === 'gallery') || ($not_import_variant_images && $img['type'] === 'variant') || ($not_import_description_images && $img['type'] === 'description'))) {
                    $product['skip_images'][] = $img_id;
                }
            }

            return B2WL_ResultBuilder::buildOk(array('product' => $product));
        }

        
        public function load_countries()
        {
            $request = B2WL_RequestHelper::build_request('get_countries', array());
            if ($request['state'] === 'error') {
                return $request;
            }

            $request = b2wl_remote_get($request['request_url']);
            if (is_wp_error($request)) {
                $result = B2WL_ResultBuilder::buildError($request->get_error_message());
            } else {

                $result = json_decode($request['body'], true);
                if (intval($result['code']) > 0) {
                    return B2WL_ResultBuilder::buildError(B2WL_Banggood::error_mapping($result['code'], $result['msg']));
                }

            }

            return $result;

        }

        public static function clean_description($description)
        {
            $html = $description;

            if (function_exists('mb_convert_encoding')) {
                $html = trim(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            } else {
                $html = htmlspecialchars_decode(utf8_decode(htmlentities($html, ENT_COMPAT, 'UTF-8', false)));
            }

            if (function_exists('libxml_use_internal_errors')) {
                libxml_use_internal_errors(true);
            }

            if (class_exists('DOMDocument')) {
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $dom->formatOutput = true;

                $tags = apply_filters('b2wl_clean_description_tags', array('script', 'head', 'meta', 'style', 'map', 'noscript', 'object', 'iframe'));

                foreach ($tags as $tag) {
                    $elements = $dom->getElementsByTagName($tag);
                    for ($i = $elements->length; --$i >= 0;) {
                        $e = $elements->item($i);
                        if ($tag == 'a') {
                            while ($e->hasChildNodes()) {
                                $child = $e->removeChild($e->firstChild);
                                $e->parentNode->insertBefore($child, $e);
                            }
                            $e->parentNode->removeChild($e);
                        } else {
                            $e->parentNode->removeChild($e);
                        }
                    }
                }

                if (!in_array('img', $tags)) {
                    $elements = $dom->getElementsByTagName('img');
                    for ($i = $elements->length; --$i >= 0;) {
                        $e = $elements->item($i);
                        $e->setAttribute('src', B2WL_Utils::clear_image_url($e->getAttribute('src')));
                    }
                }

                $html = $dom->saveHTML();
            }

            $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $html);

            $html = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) height=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) alt=".*?"/i', '$1', $html);
            $html = preg_replace('/^<!DOCTYPE.+?>/', '$1', str_replace(array('<html>', '</html>', '<body>', '</body>'), '', $html));
            $html = preg_replace("/<\/?div[^>]*\>/i", "", $html);

            $html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '', $html);
            $html = preg_replace('/<a[^>]*><\/a>/iU', '', $html); //delete empty A tags
            $html = preg_replace("/<\/?h1[^>]*\>/i", "", $html);
            $html = preg_replace("/<\/?strong[^>]*\>/i", "", $html);
            $html = preg_replace("/<\/?span[^>]*\>/i", "", $html);

            //$html = str_replace(' &nbsp; ', '', $html);
            $html = str_replace('&nbsp;', ' ', $html);
            $html = str_replace('\t', ' ', $html);
            $html = str_replace('  ', ' ', $html);

            $html = preg_replace("/http:\/\/g(\d+)\.a\./i", "https://ae$1.", $html);

            $html = preg_replace("/<[^\/>]*[^td]>([\s]?|&nbsp;)*<\/[^>]*[^td]>/", '', $html); //delete ALL empty tags
            $html = preg_replace('/<td[^>]*><\/td>/iU', '', $html); //delete empty TD tags

            $html = str_replace(array('<img', '<table'), array('<img class="img-responsive"', '<table class="table table-bordered'), $html);
            $html = force_balance_tags($html);

            return html_entity_decode($html, ENT_COMPAT, 'UTF-8');
        }

        public function get_affiliate_urls($urls, $dpu)
        {
            
            return B2WL_BanggoodAccount::getInstance()->getDeeplink($urls, $dpu);
            

            

        }

        private function add_product_variations($product, $product_stock)
        {

            $product_attributes = $product['sku_products']['attributes'];

            //prepare attributes id array
            $attributes_ids = array();

            //prepare attribute values array
            $attributes_values = array();

            foreach ($product_attributes as $attribute) {

                $tmp_array = array();
                foreach ($attribute['value'] as $val) {
                    $tmp_array[] = $val['id'];
                    $attributes_values[$val['id']] = array('name' => $val['name'], 'price' => $val['price']);
                    if (isset($val['image'])) {
                        $attributes_values[$val['id']]['image'] = $val['image'];
                    }
                }

                $attributes_ids[] = $tmp_array;
            }

            //find all attribute values combinations to build variations
            $attr_combinations = $this->combinations($attributes_ids);

            foreach ($attr_combinations as $k => $combination) {

                $variation = array();

                //fix if there is only one atribute, see example: "shipfrom:cn"
                if (!is_array($combination)) {
                    $combination = array($combination);
                }

                $variation['id'] = $product['id'] . '-' . implode('-', $combination); //todo: on migrate need take this into account

                $variation['attributes'] = array();
                $variation['attributes_names'] = array();
                $variation['currency'] = $product['currency'];
                $variation['discount'] = 0;
                $variation['quantity'] = false;
                $variation['price'] = 0;
                $variation['regular_price'] = 0;
                $variation['sku'] = $product['id'] . '-' . $k; //todo: on migrate need take this into account

                foreach ($combination as $attribute_id) {
                    $variation['attributes'][] = $attribute_id;

                    list($attr_id, $attr_val) = explode(":", $attribute_id);

                    if ($attr_id == self::$shipfrom_attribute) {
                        $variation['warehouse'] = $attr_val;
                    }

                    if (isset($variation['warehouse']) && isset($product_stock[$variation['warehouse']]) && $variation['quantity'] === false) {
                        foreach ($product_stock[$variation['warehouse']] as $attr_stock_data) {

                            if (!empty($attr_stock_data['attribute_values'])) {

                                foreach ($attr_stock_data['attribute_values'] as $s_attr_value) {

                                    if ($attr_val == $s_attr_value) {
                                        $variation['quantity'] = $this->get_stock_by_stock_message($attr_stock_data['stock'], $attr_stock_data['stock_msg']);
                                        break (2); // if stock exit from both for
                                    }
                                }
                            } else {
                                $variation['quantity'] = $this->get_stock_by_stock_message($attr_stock_data['stock'], $attr_stock_data['stock_msg']);
                            }

                        }
                    }

                    $variation['attributes_names'][] = $attributes_values[$attribute_id]['name'];
                    if (!empty($attributes_values[$attribute_id]['image'])) {
                        $variation['image'] = $attributes_values[$attribute_id]['image'];
                    }
                    $variation['price'] += floatval($attributes_values[$attribute_id]['price']);
                }

                $variation['regular_price'] = $variation['price'];

                $var_id = $variation['id'];

                $product['sku_products']['variations'][$var_id] = $variation;

            }

            return $product;

        }

        private function get_stock_by_stock_message($stock, $stock_message)
        {
            if (intval($stock) > 0) {
                return $stock;
            } else if ($stock_message == "LC_STOCK_MSG_SOLD_OUT") {
                return 0;
            } else {
                //if Banggood API sends 0 but message is not LC_STOCK_MSG_SOLD_OUT
                //then actual there is a stock, but exact value is not given
                //use default value in this case
                return b2wl_get_setting('default_stock');
            }
        }

        private function combinations($arrays, $i = 0)
        {
            if (!isset($arrays[$i])) {
                return array();
            }
            if ($i == count($arrays) - 1) {
                return $arrays[$i];
            }

            // get combinations from subsequent arrays
            $tmp = $this->combinations($arrays, $i + 1);

            $result = array();

            // concat each array from tmp with each element from $arrays[$i]
            foreach ($arrays[$i] as $v) {
                foreach ($tmp as $t) {
                    $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
                }
            }

            return $result;
        }

    }

}

<?php
/**
 * Description of B2WL_ProductDataTabController
 *
 * @author Andrey
 *
 * @autoload: b2wl_admin_init
 *
 * @ajax: true
 */
if (!class_exists('B2WL_ProductDataTabController')) {

    class B2WL_ProductDataTabController extends B2WL_AbstractController
    {

        public $tab_class = '';
        public $tab_id = '';
        public $tab_title = '';
        public $tab_icon = '';

        public function __construct()
        {
            parent::__construct();
            $this->tab_class = 'b2wl_product_data';
            $this->tab_id = 'b2wl_product_data';
            $this->tab_title = 'B2WL Data';

            add_action('admin_head', array(&$this, 'on_admin_head'));

            add_action('woocommerce_product_write_panel_tabs', array(&$this, 'product_write_panel_tabs'), 99);
            add_action('woocommerce_product_data_panels', array(&$this, 'product_data_panel_wrap'), 99);
            add_action('woocommerce_process_product_meta', array(&$this, 'process_meta_box'), 1, 2);
            add_action('woocommerce_variation_options_pricing', array(&$this, 'variation_options_pricing'), 20, 3);

            add_action('wp_ajax_b2wl_data_remove_deleted_attribute', array($this, 'ajax_remove_deleted_attribute'));
            add_action('wp_ajax_b2wl_data_remove_deleted_variation', array($this, 'ajax_remove_deleted_variation'));
            add_action('wp_ajax_b2wl_data_last_update_clean', array($this, 'ajax_last_update_clean'));
            
        }

        public function on_admin_head()
        {
            echo '<style type="text/css">#woocommerce-product-data ul.wc-tabs li.' . esc_attr($this->tab_class) . ' a::before {content: \'\f163\';}</style>';
        }

        public function product_write_panel_tabs()
        {
            ?>
            <li class="<?php echo esc_attr($this->tab_class); ?>"><a href="#<?php echo esc_attr($this->tab_id); ?>"><span><?php echo esc_js($this->tab_title); ?></span></a></li>
            <?php
}

        public function product_data_panel_wrap()
        {
            ?>
            <div id="<?php echo esc_attr($this->tab_id); ?>" class="panel <?php echo esc_attr($this->tab_class); ?> woocommerce_options_panel wc-metaboxes-wrapper" style="display:none">
                <?php $this->render_product_tab_content();?>
            </div>
            <?php
}

        public function render_product_tab_content()
        {
            global $post;

            $post_id = isset($_REQUEST['post']) ? $_REQUEST['post'] : "";

            $country_model = new B2WL_Country();

            $this->model_put('post_id', $post_id);
            $this->model_put('countries', $country_model->get_countries());

            $this->include_view("product_data_tab.php");

        }

        public function process_meta_box($post_id, $post)
        {
            if (isset($_POST['_b2w_external_id'])) {
                update_post_meta($post_id, '_b2w_external_id', sanitize_text_field($_POST['_b2w_external_id']));
            } else {
                delete_post_meta($post_id, '_b2w_external_id');
            }

            if (isset($_POST['_b2w_product_url'])) {
                update_post_meta($post_id, '_b2w_product_url', sanitize_text_field($_POST['_b2w_product_url']));
            } else {
                delete_post_meta($post_id, '_b2w_product_url');
            }

            update_post_meta($post_id, '_b2w_disable_sync', !empty($_POST['_b2w_disable_sync']) ? 1 : 0);

            update_post_meta($post_id, '_b2w_disable_var_price_change', !empty($_POST['_b2w_disable_var_price_change']) ? 1 : 0);

            update_post_meta($post_id, '_b2w_disable_var_quantity_change', !empty($_POST['_b2w_disable_var_quantity_change']) ? 1 : 0);

            update_post_meta($post_id, '_b2w_disable_add_new_variants', !empty($_POST['_b2w_disable_add_new_variants']) ? 1 : 0);

            if (!empty($_POST['_b2w_last_update'])) {
                update_post_meta($post_id, '_b2w_last_update', sanitize_text_field($_POST['_b2w_last_update']));
            } else {
                delete_post_meta($post_id, '_b2w_last_update');
            }
        }

        public function variation_options_pricing($loop, $variation_data, $variation)
        {
            if (!empty($variation_data['_banggood_regular_price']) || !empty($variation_data['_banggood_price'])) {
                echo '<p class="form-field form-row form-row-first">';
                if (!empty($variation_data['_banggood_regular_price'])) {
                    $label = sprintf(__('Banggood Regular price (%s)', 'bng2woo-lite'), get_woocommerce_currency_symbol());

                    echo '<label style="cursor: inherit;">' . esc_html($label) . ':</label>&nbsp;&nbsp;<label style="cursor: inherit;">' . esc_html(wc_format_localized_price(is_array($variation_data['_banggood_regular_price']) ? $variation_data['_banggood_regular_price'][0] : $variation_data['_banggood_regular_price'])) . '</label>';
                }
                echo '&nbsp;</p>';
                echo '<p class="form-field form-row form-row-last">';
                if (!empty($variation_data['_banggood_price'])) {
                    $label = sprintf(__('Banggood Sale price (%s)', 'bng2woo-lite'), get_woocommerce_currency_symbol());
                    echo '<label style="cursor: inherit;">' . esc_html($label) . ':</label>&nbsp;&nbsp;<label style="cursor: inherit;">' . esc_html(wc_format_localized_price(is_array($variation_data['_banggood_price']) ? $variation_data['_banggood_price'][0] : $variation_data['_banggood_price'])) . '</label>';
                }
                echo '&nbsp;</p>';
            }
        }

        public function ajax_remove_deleted_attribute()
        {
            if (!empty($_POST['post_id']) && !empty($_POST['id'])) {
                $deleted_variations_attributes = get_post_meta(intval($_POST['post_id']), '_b2w_deleted_variations_attributes', true);
                if ($deleted_variations_attributes) {
                    foreach ($deleted_variations_attributes as $k => $a) {
                        if ($_POST['id'] == 'all' || $k == sanitize_title($_POST['id'])) {
                            unset($deleted_variations_attributes[$k]);
                        }
                    }
                }
                update_post_meta(intval($_POST['post_id']), '_b2w_deleted_variations_attributes', $deleted_variations_attributes);
            }
            echo json_encode(B2WL_ResultBuilder::buildOk());
            wp_die();
        }

        public function ajax_remove_deleted_variation()
        {
            if (!empty($_POST['post_id'])) {
                $b2wl_skip_meta = get_post_meta(intval($_POST['post_id']), "_b2w_skip_meta", true);
                $b2wl_skip_meta = $b2wl_skip_meta ? $b2wl_skip_meta : array('skip_vars' => array(), 'skip_images' => array());
                if ($_POST['id'] == 'all') {
                    $b2wl_skip_meta['skip_vars'] = array();
                } else {
                    $b2wl_skip_meta['skip_vars'] = array_filter(array_diff($b2wl_skip_meta['skip_vars'], array($_POST['id'])));
                }
                update_post_meta(intval($_POST['post_id']), "_b2w_skip_meta", $b2wl_skip_meta);
            }
            echo json_encode(B2WL_ResultBuilder::buildOk());
            wp_die();
        }

        public function ajax_last_update_clean()
        {
            if (!empty($_POST['post_id']) && !empty($_POST['type'])) {
                if ($_POST['type'] === 'product') {
                    delete_post_meta(intval($_POST['post_id']), '_b2w_last_update');
                }
            }
            echo json_encode(B2WL_ResultBuilder::buildOk());
            wp_die();
        }
        
    }

}

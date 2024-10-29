<?php
/* * class
 * Description of B2WL_WooCommerceProductListController
 *
 * @author MA_GROUP
 *
 * @autoload: b2wl_admin_init
 *
 * @ajax: true
 */
if (!class_exists('B2WL_WooCommerceProductListController')) {

    class B2WL_WooCommerceProductListController
    {

        private $bulk_actions = array();
        private $bulk_actions_text = array();

        public function __construct()
        {
            add_action('admin_footer-edit.php', array($this, 'scripts'));
            add_action('load-edit.php', array($this, 'bulk_actions'));
            add_filter('post_row_actions', array($this, 'row_actions'), 2, 150);
            add_action('admin_enqueue_scripts', array($this, 'assets'));
            add_action('admin_init', array($this, 'init'));

            add_action('wp_ajax_b2wl_product_info', array($this, 'ajax_product_info'));
            

            add_action('wp_ajax_b2wl_get_product_id', array($this, 'ajax_get_product_id'));
        }

        public function init()
        {
            
            list($this->bulk_actions, $this->bulk_actions_text) = apply_filters('b2wl_wcpl_bulk_actions_init', array($this->bulk_actions, $this->bulk_actions_text));
        }

        public function row_actions($actions, $post)
        {
            if ('product' === $post->post_type) {
                $external_id = get_post_meta($post->ID, "_b2w_external_id", true);
                if ($external_id) {
                    $actions = array_merge($actions, array('b2wl_product_info' => sprintf('<a class="b2wl-product-info" id="b2wl-%1$d" data-external-id="%2$s" href="#">%3$s</a>', $post->ID, $external_id, 'Banggood Info')));
                }
            }

            return $actions;
        }

        public function assets()
        {

            wp_enqueue_style('b2wl-wc-pl-style', B2WL()->plugin_url() . '/assets/css/wc_pl_style.css', array(), B2WL()->version);

            wp_style_add_data('b2wl-wc-pl-style', 'rtl', 'replace');

            wp_enqueue_script('b2wl-wc-pl-script', B2WL()->plugin_url() . '/assets/js/wc_pl_script.js', ['jquery-ui-core', 'jquery-ui-dialog'], B2WL()->version);

            wp_enqueue_script('b2wl-sprintf-script', B2WL()->plugin_url() . '/assets/js/sprintf.js', array(), B2WL()->version);

            $lang_data = array(
                'please_wait_data_loads' => _x('Please wait, data loads..', 'Status', 'bng2woo-lite'),
                'process_update_d_of_d' => _x('Process update %d of %d.', 'Status', 'bng2woo-lite'),
                'process_update_d_of_d_erros_d' => _x('Process update %d of %d. Errors: %d.', 'Status', 'bng2woo-lite'),
                'complete_result_updated_d_erros_d' => _x('Complete! Result updated: %d; errors: %d.', 'Status', 'bng2woo-lite'),
            );

            $localizator = B2WL_BanggoodLocalizator::getInstance();

            wp_localize_script('b2wl-wc-pl-script', 'b2wl_wc_pl_script',
                array('lang' => $lang_data,
                    'currency' => $localizator->currency,
                    'chrome_ext_import' => b2wl_check_defined('B2WL_CHROME_EXT_IMPORT'),
                    'chrome_url' => B2WL()->chrome_url,
                )
            );
        }

        public function scripts()
        {
            global $post_type;

            if ($post_type == 'product') {

                foreach ($this->bulk_actions as $action) {
                    $text = $this->bulk_actions_text[$action];
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('<option>').val('<?php echo esc_js($action); ?>').text('<?php echo esc_js($text); ?>').appendTo("select[name='action']");
                            jQuery('<option>').val('<?php echo esc_js($action); ?>').text('<?php echo esc_js($text); ?>').appendTo("select[name='action2']");
                        });
                    </script>
                    <?php
}
            }
        }

        public function bulk_actions()
        {
            global $typenow;
            $post_type = $typenow;

            if ($post_type == 'product') {

                $wp_list_table = _get_list_table('WP_Posts_List_Table');
                $action = $wp_list_table->current_action();

                $allowed_actions = $this->bulk_actions;
                if (!in_array($action, $allowed_actions)) {
                    return;
                }

                check_admin_referer('bulk-posts');

                // make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
                if (isset($_REQUEST['post'])) {
                    $post_ids = array_map('intval', $_REQUEST['post']);
                }

                if (empty($post_ids)) {
                    return;
                }

                $sendback = remove_query_arg(array_merge($allowed_actions, array('untrashed', 'deleted', 'ids')), wp_get_referer());
                if (!$sendback) {
                    $sendback = admin_url("edit.php?post_type=$post_type");
                }

                $pagenum = $wp_list_table->get_pagenum();
                $sendback = add_query_arg('paged', $pagenum, $sendback);

                $sendback = apply_filters('b2wl_wcpl_bulk_actions_perform', $sendback, $action, $post_ids);

                $sendback = remove_query_arg(array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view'), $sendback);

                wp_redirect($sendback);
                exit();
            }
        }

        public function ajax_product_info()
        {
            $result = array("state" => "ok", "data" => "");

            $post_id = isset($_POST['id']) ? intval($_POST['id']) : false;

            if (!$post_id) {
                $result['state'] = 'error';
                echo json_encode($result);
                wp_die();
            }

            $external_id = get_post_meta($post_id, "_b2w_external_id", true);

            $time_value = get_post_meta($post_id, '_b2w_last_update', true);
            $time_value = $time_value ? date("Y-m-d H:i:s", $time_value) : 'not updated';

            $product_url = get_post_meta($post_id, '_product_url', true);
            if (!$product_url) {
                $product_url = get_post_meta($post_id, '_b2w_original_product_url', true);
            }

            $content = array();

            $content[] = "Product: <a target='_blank' href='" . $product_url . "'>here</a>";

            $seller_url = get_post_meta($post_id, '_b2w_seller_url', true);
            $seller_name = get_post_meta($post_id, '_b2w_seller_name', true);

            if ($seller_url && $seller_name) {
                $content[] = "Seller: <a target='_blank' href='" . $seller_url . "'>" . $seller_name . "</a>";
            }

            $content[] = "External ID: <span class='b2wl_value'>" . $external_id . "</span>";
            $content[] = "Last auto-update: <span class='b2wl_value'>" . $time_value . "</span>";

            $content = apply_filters('b2wl_ajax_product_info', $content, $post_id, $external_id);
            $result['data'] = array('content' => $content, 'id' => $post_id);

            echo json_encode($result);
            wp_die();
        }
        
        public function ajax_get_product_id()
        {
            if (!empty($_POST['post_id'])) {
                $woocommerce_model = new B2WL_Woocommerce();
                $id = $woocommerce_model->get_product_external_id(intval($_POST['post_id']));
                if ($id) {
                    $result = B2WL_ResultBuilder::buildOk(array('id' => $id));
                } else {
                    $result = B2WL_ResultBuilder::buildError('uncknown ID');
                }
            } else {
                $result = B2WL_ResultBuilder::buildError("get_product_id: waiting for ID...");
            }
            echo json_encode($result);
            wp_die();
        }

    }

}

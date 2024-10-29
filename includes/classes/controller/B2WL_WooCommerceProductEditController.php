<?php

/* * class
 * Description of B2WL_WooCommerceProductEditController
 *
 * @author andrey
 *
 * @autoload: b2wl_admin_init
 *
 * @ajax: true
 */
if (!class_exists('B2WL_WooCommerceProductEditController')) {

    class B2WL_WooCommerceProductEditController extends B2WL_AbstractController
    {

        public function __construct()
        {
            parent::__construct();

            add_action('current_screen', array($this, 'current_screen'));

            add_filter('get_sample_permalink_html', array($this, 'get_sample_permalink_html'), 10, 2);
        }

        public function get_sample_permalink_html($return, $id)
        {

            $external_id = get_post_meta($id, '_b2w_external_id', true);

            if ($external_id) {
                $return .= '<button type="button" data-id="' . $id . '" class="sync-bng-product button button-small hide-if-no-js">' . __("Banggood Sync", 'bng2woo-lite') . '</button>';
            }

            return $return;
        }

        public function current_screen($current_screen)
        {
            if ($current_screen->in_admin() && ($current_screen->id == 'product' || $current_screen->id == 'bng2woo-lite_page_b2wl_import')) {
                wp_enqueue_script('b2wl-admin-script', B2WL()->plugin_url() . '/assets/js/admin_script.js', array('jquery'), B2WL()->version);

                $lang_data = array(
                    'process_loading_d_of_d_erros_d' => _x('Process loading %d of %d. Errors: %d.', 'Status', 'bng2woo-lite'),
                    'load_button_text' => _x('Load %d images', 'Status', 'bng2woo-lite'),
                    'all_images_loaded_text' => _x('All images loaded', 'Status', 'bng2woo-lite'),
                );
                wp_localize_script('b2wl-admin-script', 'b2wl_external_images_data', array('lang' => $lang_data));

                $lang_data = array(
                    'sync_successfully' => _x('Synchronized successfully.', 'Status', 'bng2woo-lite'),
                    'sync_failed' => _x('Sync failed.', 'Status', 'bng2woo-lite'),
                );
                wp_localize_script('b2wl-admin-script', 'b2wl_sync_data', array('lang' => $lang_data));

                wp_enqueue_style('b2wl-admin-style', B2WL()->plugin_url() . '/assets/css/admin_style.css', array(), B2WL()->version);

                /* wp_enqueue_style('b2wl-wc-spectrum-style', B2WL()->plugin_url() . '/assets/js/spectrum/spectrum.css', array(), B2WL()->version);
            wp_enqueue_script('b2wl-wc-spectrum-script', B2WL()->plugin_url() . '/assets/js/spectrum/spectrum.js', array(), B2WL()->version);

            wp_enqueue_script('tui-image-editor-fabric', B2WL()->plugin_url() . '/assets/js/image-editor/fabric.js', array('jquery'), B2WL()->version);
            wp_enqueue_script('tui-code-snippet', B2WL()->plugin_url() . '/assets/js/image-editor/tui-code-snippet.min.js', array('jquery'), B2WL()->version);
            wp_enqueue_script('tui-image-editor-FileSaver', B2WL()->plugin_url() . '/assets/js/image-editor/FileSaver.min.js', array('jquery'), B2WL()->version);
            wp_enqueue_script('tui-image-editor', B2WL()->plugin_url() . '/assets/js/image-editor/tui-image-editor.js', array('jquery'), B2WL()->version);

            wp_enqueue_script('b2wl-wc-pe-script', B2WL()->plugin_url() . '/assets/js/wc_pe_script.js', array(), B2WL()->version);
            wp_enqueue_style('b2wl-wc-pe-style', B2WL()->plugin_url() . '/assets/css/wc_pe_style.css', array(), B2WL()->version);
             */
            }
        }

    }

}

<?php

/* * class
 * Description of B2WL_AbstractPage
 *
 * @author andrey
 *
 * @position: 2
 */

if (!class_exists('B2WL_AbstractAdminPage')) {

    abstract class B2WL_AbstractAdminPage extends B2WL_AbstractController
    {

        private $page_title;
        private $menu_title;
        private $capability;
        private $menu_slug;
        private $menu_as_link;
        private $style_assets = array();
        private $script_assets = array();
        private $script_data_assets = array();

        public function __construct($page_title, $menu_title, $capability, $menu_slug, $priority = 10, $menu_as_link = false)
        {
            parent::__construct(B2WL()->plugin_path() . '/view/');

            if (is_admin()) {
                $this->init($page_title, $menu_title, $capability, $menu_slug, $priority, $menu_as_link);

                add_action('b2wl_admin_assets', array($this, 'admin_register_assets'), 1);

                add_action('b2wl_admin_assets', array($this, 'admin_enqueue_assets'), 2);

                add_action('wp_loaded', array($this, 'before_render_action'));

                if ($this->is_current_page() && !B2WL_Woocommerce::is_woocommerce_installed() && !has_action('admin_notices', array($this, 'woocomerce_check_error'))) {
                    add_action('admin_notices', array($this, 'woocomerce_check_error'));
                }

                if ($this->is_current_page() && !has_action('admin_notices', array($this, 'global_system_message'))) {
                    add_action('admin_notices', array($this, 'global_system_message'));
                }
            }
        }

        public function woocomerce_check_error()
        {
            echo '<div id="message2222" class="notice error is-dismissible"><p>' . __('Bng2Woo Lite notice! Please install the <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> plugin first.', 'bng2woo-lite') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        }

        public function global_system_message()
        {
            $system_message = b2wl_get_setting('system_message');
            if ($system_message && !empty($system_message['message'])) {
                $message_class = 'updated';
                if ($system_message['type'] == 'error') {
                    $message_class = 'error';
                }
                echo '<div id="b2wl-system-message" class="notice ' . $message_class . ' is-dismissible"><p>' . $system_message['message'] . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
            }
        }

        protected function init($page_title, $menu_title, $capability, $menu_slug, $priority, $menu_as_link)
        {
            $this->page_title = $page_title;
            $this->menu_title = $menu_title;
            $this->capability = $capability;
            $this->menu_slug = $menu_slug;
            $this->menu_as_link = $menu_as_link;
            add_action('b2wl_init_admin_menu', array($this, 'add_submenu_page'), $priority);
        }

        public function add_submenu_page($parent_slug)
        {
            if ($this->menu_as_link) {
                $page_id = add_submenu_page($parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->menu_slug);
            } else {
                $page_id = add_submenu_page($parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array($this, 'render'));
            }

            add_action("load-$page_id", array($this, 'configure_screen_options'));
        }

        public function before_render_action()
        {
            if ($this->is_current_page()) {
                $this->before_admin_render();
            }
        }

        public function before_admin_render()
        {

        }

        public function configure_screen_options()
        {
        }

        abstract public function render($params = array());

        public function add_style($handle, $src, $deps = array(), $ver = false, $media = 'all')
        {
            $this->style_assets[] = array('handle' => $handle, 'src' => $src, 'deps' => $deps, 'ver' => $ver, 'media' => $media);
        }

        public function add_script($handle, $src, $deps = array(), $ver = false, $in_footer = false)
        {
            $this->script_assets[] = array('handle' => $handle, 'src' => $src, 'deps' => $deps, 'ver' => $ver, 'in_footer' => $in_footer);
        }

        public function add_data_script($handle, $name, $data)
        {
            $this->script_data_assets[] = array('handle' => $handle, 'name' => $name, 'data' => $data);
        }

        public function admin_register_assets()
        {
            if ($this->is_current_page()) {
                if (!wp_style_is('b2wl-admin-style', 'registered')) {
                    wp_register_style('b2wl-admin-style', B2WL()->plugin_url() . '/assets/css/admin_style.css', array(), B2WL()->version);
                }

                if (!wp_script_is('b2wl-sprintf-script', 'registered')) {
                    wp_register_script('b2wl-sprintf-script', B2WL()->plugin_url() . '/assets/js/sprintf.js', array(), B2WL()->version);
                }

                if (!wp_script_is('b2wl-admin-script', 'registered')) {
                    wp_register_script('b2wl-admin-script', B2WL()->plugin_url() . '/assets/js/admin_script.js', array('jquery'), B2WL()->version);
                }

                if (!wp_script_is('b2wl-admin-svg', 'registered')) {
                    wp_register_script('b2wl-admin-svg', B2WL()->plugin_url() . '/assets/js/svg.min.js', array('jquery', 'b2wl-admin-script'), B2WL()->version);
                }

                /* select2 */
                if (!wp_style_is('b2wl-select2-style', 'registered')) {
                    wp_register_style('b2wl-select2-style', B2WL()->plugin_url() . '/assets/js/select2/css/select2.min.css', array(), B2WL()->version);
                }
                if (!wp_script_is('b2wl-select2-js', 'registered')) {
                    wp_register_script('b2wl-select2-js', B2WL()->plugin_url() . '/assets/js/select2/js/select2.min.js', array('jquery'), B2WL()->version);
                }

                /*jquery.lazyload*/
                if (!wp_script_is('b2wl-lazyload-js', 'registered')) {
                    wp_register_script('b2wl-lazyload-js', B2WL()->plugin_url() . '/assets/js/jquery/jquery.lazyload.js', array('jquery'), B2WL()->version);
                }

                /* bootstrap */
                if (!wp_style_is('b2wl-bootstrap-style', 'registered')) {
                    wp_register_style('b2wl-bootstrap-style', B2WL()->plugin_url() . '/assets/js/custom-bootstrap/css/bootstrap.min.css', array(), B2WL()->version);
                }
                if (!wp_script_is('b2wl-bootstrap-js', 'registered')) {
                    wp_register_script('b2wl-bootstrap-js', B2WL()->plugin_url() . '/assets/js/custom-bootstrap/js/bootstrap.min.js', array('jquery'), B2WL()->version);
                }

                foreach ($this->style_assets as $s) {
                    if (!wp_style_is($s['handle'], 'registered')) {
                        wp_register_style($s['handle'], B2WL()->plugin_url() . $s['src'], $s['deps'], !empty($s['ver']) ? $s['ver'] : B2WL()->version, $s['media']);
                    }
                }

                foreach ($this->script_assets as $s) {
                    if (!wp_script_is($s['handle'], 'registered')) {
                        wp_register_script($s['handle'], B2WL()->plugin_url() . $s['src'], $s['deps'], !empty($s['ver']) ? $s['ver'] : B2WL()->version, $s['in_footer']);
                    }
                }

                $lang_data = array();
                wp_localize_script('b2wl-admin-script', 'b2wl_common_data', array('baseurl' => B2WL()->plugin_url() . '/', 'lang' => apply_filters('b2wl_configure_lang_data', $lang_data)));

                foreach ($this->script_data_assets as $d) {
                    wp_localize_script($d['handle'], $d['name'], $d['data']);
                }
            }
        }

        public function admin_enqueue_assets($page)
        {
            if ($this->is_current_page()) {
                wp_enqueue_script('jquery-ui-sortable');

                wp_enqueue_script('jquery-effects-core');

                if (!wp_style_is('b2wl-admin-style', 'enqueued')) {
                    wp_enqueue_style('b2wl-admin-style');
                    wp_style_add_data('b2wl-admin-style', 'rtl', 'replace');
                }
                if (!wp_style_is('b2wl-admin-style-new', 'enqueued')) {
                    wp_enqueue_style('b2wl-admin-style-new');
                }

                if (!wp_script_is('b2wl-sprintf-script', 'enqueued')) {
                    wp_enqueue_script('b2wl-sprintf-script');
                }
                if (!wp_script_is('b2wl-admin-script', 'enqueued')) {
                    wp_enqueue_script('b2wl-admin-script');
                }
                if (!wp_script_is('b2wl-admin-svg', 'enqueued')) {
                    wp_enqueue_script('b2wl-admin-svg');
                }

                /* select2 */
                if (!wp_style_is('b2wl-select2-style', 'enqueued')) {
                    wp_enqueue_style('b2wl-select2-style');
                }
                if (!wp_script_is('b2wl-select2-js', 'enqueued')) {
                    wp_enqueue_script('b2wl-select2-js');
                }

                /*jquery.lazyload*/
                if (!wp_script_is('b2wl-lazyload-js', 'enqueued')) {
                    wp_enqueue_script('b2wl-lazyload-js');
                }

                /* bootstrap */
                if (!wp_style_is('b2wl-bootstrap-style', 'enqueued')) {
                    wp_enqueue_style('b2wl-bootstrap-style');
                }
                if (!wp_script_is('b2wl-bootstrap-js', 'enqueued')) {
                    wp_enqueue_script('b2wl-bootstrap-js');
                }

                foreach ($this->style_assets as $style) {
                    if (!wp_style_is($style['handle'], 'enqueued')) {
                        wp_enqueue_style($style['handle']);
                    }
                }

                foreach ($this->script_assets as $script) {
                    if (!wp_script_is($script['handle'], 'enqueued')) {
                        wp_enqueue_script($script['handle']);
                    }
                }

            }
        }

        protected function is_current_page()
        {
            return is_admin() && (
                (isset($_REQUEST['page']) && $_REQUEST['page'] && $this->menu_slug == $_REQUEST['page']) ||
                (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] && strpos($this->menu_slug, $_REQUEST['post_type']) !== false)
            );
        }

    }

}

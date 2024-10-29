<?php
/*
Plugin Name: Dropshipping with Banggood for WooCommerce (Lite version)
Description: Dropshipping with Banggood for WooCommerce (Lite version) is a WordPress plugin created for Banggood Drop Shipping and Affiliate marketing
Text Domain: bng2woo-lite
Domain Path: /languages
Version: 1.2.11
Author: MA-Group
Author URI: https://ali2woo.com/contact/
License: GPLv2+
Tested up to: 5.9
WC tested up to: 6.1
Requires PHP: 7.0
WC requires at least: 5.0
 */

if (!defined('B2WL_PLUGIN_FILE')) {
    define('B2WL_PLUGIN_FILE', __FILE__);
}

if (!class_exists('B2WL_Main')) {

    class B2WL_Main
    {

        /**
         * @var The single instance of the class
         */
        protected static $_instance = null;

        /**
         * @var string Bng2Woo Lite plugin version
         */
        public $version;

        /**
         * @var string Bng2Woo Lite plugin version
         */
        public $plugin_name;

        /**
         * @var string chrome extension url
         */
        public $chrome_url = 'https://chrome.google.com/webstore/detail/banggood-dropshipping/iacimipedpoofkikilgbbllfgnngmpkk';

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function __construct()
        {

            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            $plugin_data = get_plugin_data(B2WL_PLUGIN_FILE);

            $this->version = $plugin_data['Version'];
            $this->plugin_name = plugin_basename(B2WL_PLUGIN_FILE);

            require_once $this->plugin_path() . '/includes/libs/wp-background-processing/wp-background-processing.php';

            include_once $this->plugin_path() . '/includes/settings.php';
            include_once $this->plugin_path() . '/includes/functions.php';

            include_once $this->plugin_path() . '/includes/init.php';
            B2WL_Init::init_classes($this->plugin_path() . '/includes/classes', 'b2wl_init');
            B2WL_Init::init_addons($this->plugin_path() . '/addons');

            include_once $this->plugin_path() . "/includes/libs/b2wl_json_api/b2wl_json_api.php";
            B2WL_Json_Api_Configurator::init('b2wl_dashboard');

            if (!class_exists('Requests')) {
                include_once $this->plugin_path() . '/includes/libs/Requests/Requests.php';
                Requests::register_autoloader();
            }

            // Need to activate cron healthcheck
            B2WL_ImportProcess::init();

            register_activation_hook(B2WL_PLUGIN_FILE, array($this, 'install'));
            register_deactivation_hook(B2WL_PLUGIN_FILE, array($this, 'uninstall'));

            add_action('admin_menu', array($this, 'admin_menu'));

            add_action('admin_enqueue_scripts', array($this, 'admin_assets'));

            add_action('wp_enqueue_scripts', array($this, 'assets'));
        }

        /**
         * Path to Bng2Woo Lite plugin root url
         */
        public function plugin_url()
        {
            return untrailingslashit(plugins_url('/', B2WL_PLUGIN_FILE));
        }

        /**
         * Path to Bng2Woo Lite plugin root dir
         */
        public function plugin_path()
        {
            return untrailingslashit(plugin_dir_path(B2WL_PLUGIN_FILE));
        }

        public function install()
        {
            do_action('b2wl_install');
        }

        public function uninstall()
        {
            do_action('b2wl_uninstall');
        }

        public function assets($page)
        {
            do_action('b2wl_assets', $page);
        }

        public function admin_assets($page)
        {
            do_action('b2wl_admin_assets', $page);
        }

        public function admin_menu()
        {
            do_action('b2wl_before_admin_menu');

            add_menu_page(__('Bng2Woo Lite', 'bng2woo-lite'), __('Bng2Woo Lite', 'bng2woo-lite'), 'import', 'b2wl_dashboard', '', plugins_url('assets/img/icon.png', B2WL_PLUGIN_FILE));

            do_action('b2wl_init_admin_menu', 'b2wl_dashboard');
        }

    }

}

/**
 * Returns the main instance of B2WL_Main to prevent the need to use globals.
 *
 * @return B2WL_Main
 */
if (!function_exists('B2WL')) {

    function B2WL()
    {
        return B2WL_Main::instance();
    }

}

$bng2woo_lite = B2WL();

/**
 * Bng2Woo Lite global init action
 */
do_action('b2wl_init');

if (is_admin()) {
    do_action('b2wl_admin_init');
} else {
    do_action('b2wl_frontend_init');
}

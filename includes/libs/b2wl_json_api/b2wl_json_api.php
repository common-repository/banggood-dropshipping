<?php

if (!defined('B2WL_JSON_API_VERSION')) {
    define('B2WL_JSON_API_VERSION', "1.0.0");
}

if (!defined('B2WL_JSON_API_DIR')) {
    define('B2WL_JSON_API_DIR', dirname(__FILE__));
}

@include_once B2WL_JSON_API_DIR . "/singletons/api.php";
@include_once B2WL_JSON_API_DIR . "/singletons/query.php";
@include_once B2WL_JSON_API_DIR . "/singletons/response.php";

if (!class_exists('B2WL_Json_Api_Configurator')) {
    class B2WL_Json_Api_Configurator
    {

        private function __construct()
        {}

        public static function init($root_menu_slug)
        {

            $configurator = new B2WL_Json_Api_Configurator();
            $configurator->root_menu_slug = $root_menu_slug;

            add_action('init', array($configurator, 'json_api_init'));

            add_action('b2wl_install', array($configurator, 'activation'));
            add_action('b2wl_uninstall', array($configurator, 'deactivation'));
        }

        public function activation()
        {
            // Add the rewrite rule on activation
            global $wp_rewrite;
            add_filter('rewrite_rules_array', array(new B2WL_Json_Api_Configurator(), 'json_api_rewrites'));
            $wp_rewrite->flush_rules();
        }

        public function deactivation()
        {
            // Remove the rewrite rule on deactivation
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }

        public function json_api_init()
        {
            global $b2wl_json_api;
            if (phpversion() < 5) {
                add_action('admin_notices', array($this, 'json_api_php_version_warning'));
                return;
            }
            if (!class_exists('B2WL_JSON_API')) {
                add_action('admin_notices', array($this, 'json_api_class_warning'));
                return;
            }

            add_filter('rewrite_rules_array', array($this, 'json_api_rewrites'));

            $b2wl_json_api = new B2WL_JSON_API(empty($this->root_menu_slug) ? '' : $this->root_menu_slug);
        }

        public function json_api_rewrites($wp_rules)
        {
            $base = b2wl_get_setting('json_api_base');
            if (empty($base)) {
                return $wp_rules;
            }
            $json_api_rules = array(
                "$base\$" => 'index.php?b2w-json=info',
                "$base/(.+)\$" => 'index.php?b2w-json=$matches[1]',
            );
            return array_merge($json_api_rules, $wp_rules);
        }

        public function json_api_php_version_warning()
        {
            echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Sorry, JSON API requires PHP version 5.0 or greater.</p></div>";
        }

        public function json_api_class_warning()
        {
            echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Oops, B2WL_JSON_API class not found. If you've defined a B2WL_JSON_API_DIR constant, double check that the path is correct.</p></div>";
        }

    }
}

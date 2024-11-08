<?php

if (!class_exists('B2WL_JSON_API_Query')) {

    class B2WL_JSON_API_Query
    {

        // Default values
        protected $defaults = array(
            'date_format' => 'Y-m-d H:i:s',
            'read_more' => 'Read more',
        );

        public function __construct()
        {
            // Register JSON API query vars
            add_filter('query_vars', array(&$this, 'query_vars'));
        }

        public function get($key)
        {
            if (is_array($key)) {
                $result = array();
                foreach ($key as $k) {
                    $result[$k] = $this->get($k);
                }
                return $result;
            }
            $query_var = (isset($_REQUEST[$key])) ? $_REQUEST[$key] : null;
            $wp_query_var = $this->wp_query_var($key);
            if ($wp_query_var) {
                return $wp_query_var;
            } else if ($query_var) {
                return $this->strip_magic_quotes($query_var);
            } else if (isset($this->defaults[$key])) {
                return $this->defaults[$key];
            } else {
                return null;
            }
        }

        public function __get($key)
        {
            return $this->get($key);
        }

        public function __isset($key)
        {
            return ($this->get($key) !== null);
        }

        public function wp_query_var($key)
        {
            $wp_translation = array(
                'b2w-json' => 'b2w-json',
                'post_id' => 'p',
                'post_slug' => 'name',
                'page_id' => 'page_id',
                'page_slug' => 'name',
                'category_id' => 'cat',
                'category_slug' => 'category_name',
                'tag_id' => 'tag_id',
                'tag_slug' => 'tag',
                'author_id' => 'author',
                'author_slug' => 'author_name',
                'search' => 's',
                'order' => 'order',
                'order_by' => 'orderby',
            );
            if ($key == 'date') {
                $date = null;
                if (get_query_var('year')) {
                    $date = get_query_var('year');
                }
                if (get_query_var('monthnum')) {
                    $month = get_query_var('monthnum');
                    if ($month < 10) {
                        $month = "0$month";
                    }
                    $date .= $month;
                }
                if (get_query_var('day')) {
                    $day = get_query_var('day');
                    if ($day < 10) {
                        $day = "0$day";
                    }
                    $date .= $day;
                }
                return $date;
            } else if (isset($wp_translation[$key])) {
                return get_query_var($wp_translation[$key]);
            } else {
                return null;
            }
        }

        public function strip_magic_quotes($value)
        {
            // Deprecated!
            // if (get_magic_quotes_gpc()) {
            //     return stripslashes($value);
            // } else {
            //     return $value;
            // }
            return $value;
        }

        public function query_vars($wp_vars)
        {
            $wp_vars[] = 'b2w-json';
            return $wp_vars;
        }

        public function is_valid_api_key($key)
        {
            if (!empty($key)) {
                $api_keys = b2wl_get_setting('api_keys', array());
                foreach ($api_keys as $k) {
                    if ($k['id'] === $key) {
                        return true;
                    }
                }
            }
            return false;
        }

        public function get_controller()
        {
            $json = $this->get('b2w-json');

            if (empty($json)) {
                return false;
            }

            if (preg_match('/^[a-zA-Z_]+$/', $json)) {
                return $this->get_legacy_controller($json);
            } else if (preg_match('/^([a-zA-Z0-9_]+)(\/|\.)[a-zA-Z0-9_]+$/', $json, $matches)) {
                return $matches[1];
            } else {
                return 'core';
            }
        }

        public function get_legacy_controller($json)
        {
            global $b2wl_json_api;
            if ($json == 'submit_comment') {
                if ($b2wl_json_api->controller_is_active('respond')) {
                    return 'respond';
                } else {
                    $b2wl_json_api->error("The 'submit_comment' method has been removed from the Core controller. To use this method you must enable the Respond controller from WP Admin > Settings > JSON API.");
                }
            } else if ($json == 'create_post') {
                if ($b2wl_json_api->controller_is_active('posts')) {
                    return 'posts';
                } else {
                    $b2wl_json_api->error("The 'create_post' method has been removed from the Core controller. To use this method you must enable the Posts controller from WP Admin > Settings > JSON API.");
                }
            } else {
                return 'core';
            }
        }

        public function get_method($controller)
        {

            global $b2wl_json_api;

            // Returns an appropriate API method name or false. Four possible outcomes:
            //   1. API isn't being invoked at all (return false)
            //   2. A specific API method was requested (return method name)
            //   3. A method is chosen implicitly on a given WordPress page
            //   4. API invoked incorrectly (return "error" method)
            //
            // Note:
            //   The implicit outcome (3) is invoked by setting the b2w-json query var to a
            //   non-empty value on any WordPress page:
            //     * http://example.org/2009/11/10/hello-world/?b2w-json=1 (get_post)
            //     * http://example.org/2009/11/?b2w-json=1 (get_date_posts)
            //     * http://example.org/category/foo?b2w-json=1 (get_category_posts)

            $method = $this->get('b2w-json');
            if (strpos($method, '/') !== false) {
                $method = substr($method, strpos($method, '/') + 1);
            } else if (strpos($method, '.') !== false) {
                $method = substr($method, strpos($method, '.') + 1);
            }

            if (empty($method)) {
                // Case 1: we're not being invoked (done!)
                return false;
            } else if (method_exists("B2WL_JSON_API_{$controller}_Controller", $method)) {
                // Case 2: an explicit method was specified
                return $method;
            } else if ($controller == 'core') {
                $b2wl_json_api->error("Unknown method '$method'.");
                return 'info';
            }
            // Case 4: either the method doesn't exist or we don't support the page implicitly
            return 'error';
        }
    }

}

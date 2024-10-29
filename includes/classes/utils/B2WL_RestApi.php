<?php

/**
 * Description of B2WL_RestApi
 *
 * @author Andrey
 * 
 * @autoload: b2wl_init
 */

if (!class_exists('B2WL_RestApi')) {

    class B2WL_RestApi {
        public function __construct() {
            add_action('rest_api_init', array($this, 'register_routes'));
        }
        
        public function register_routes() {
            register_rest_route('b2wl-api/v1', '/info', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'info'),
                'permission_callback' => '__return_true'
            ));
        }

        public function info($request) {
            $result = array();

            $result['plugin_version'] = B2WL()->version;
            
            return rest_ensure_response($result);
        }       
    }
}

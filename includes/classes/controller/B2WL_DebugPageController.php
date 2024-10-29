<?php

/**
 * Description of B2WL_DebugPageController
 *
 * @author andrey
 * 
 * @autoload: b2wl_admin_init
 */
if (!class_exists('B2WL_DebugPageController')) {

    class B2WL_DebugPageController extends B2WL_AbstractAdminPage {

        public function __construct() {
            if (b2wl_check_defined('B2WL_DEBUG_PAGE')) {
                parent::__construct(__('Debug', 'bng2woo-lite'), __('Debug', 'bng2woo-lite'), 'edit_plugins', 'bng2woo-lite', 1100);
            }
        }

        public function render($params = array()) {
            echo "<br/><b>DEBUG</b><br/>";
            // $banggood = new B2WL_Banggood();
            // $res = $banggood->load_categories();
            // error_log(json_encode($res));
            // echo "<pre>";print_r($res);echo "</pre>";
        }
        
    }
}

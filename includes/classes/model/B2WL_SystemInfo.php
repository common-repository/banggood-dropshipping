<?php

/**
 * Description of B2WL_SystemInfo
 *
 * @author Andrey
 *
 * @autoload: b2wl_admin_init
 */

if (!class_exists('B2WL_SystemInfo')) {
    class B2WL_SystemInfo
    {

        public function __construct()
        {
            add_action('wp_ajax_b2wl_ping', array($this, 'ajax_ping'));
            add_action('wp_ajax_nopriv_b2wl_ping', array($this, 'ajax_ping'));

            add_action('wp_ajax_b2wl_clear_log_file', array($this, 'ajax_clear_log_file'));
            add_action('wp_ajax_b2wl_clean_import_queue', array($this, 'ajax_clean_import_queue'));
            add_action('wp_ajax_b2wl_run_cron_import_queue', array($this, 'ajax_run_cron'));
        }

        public function ajax_clear_log_file()
        {
            B2WL_Logs::getInstance()->delete();
            echo json_encode(array('state' => 'ok'));
            wp_die();
        }

        public function ajax_clean_import_queue()
        {
            $import_process = new B2WL_ImportProcess();
            $import_process->clean_queue();
            echo json_encode(array('state' => 'ok'));
            wp_die();
        }

        public function ajax_run_cron()
        {
            $import_process = new B2WL_ImportProcess();
            $import_process->dispatch();
            echo json_encode(array('state' => 'ok'));
            wp_die();
        }

        public function ajax_ping()
        {
            echo json_encode(array('state' => 'ok'));
            wp_die();
        }

        public static function ping()
        {
            $result = array();
            $request = wp_remote_post(admin_url('admin-ajax.php') . "?action=b2wl_ping");
            if (is_wp_error($request)) {
                $result = B2WL_ResultBuilder::buildError($request->get_error_message());
            } else if (intval($request['response']['code']) != 200) {
                $result = B2WL_ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
            } else {
                $result = json_decode($request['body'], true);
            }
            return $result;
        }

        public static function server_ping()
        {
            $result = array();
            $ping_url = 'https://api.ali2woo.com/banggood/v1/ping.php?r=' . mt_rand();
            $request = b2wl_remote_get($ping_url);
            if (is_wp_error($request)) {
                if (file_get_contents($ping_url)) {
                    $result = B2WL_ResultBuilder::buildError('b2wl_remote_get error');
                } else {
                    $result = B2WL_ResultBuilder::buildError($request->get_error_message());
                }
            } else if (intval($request['response']['code']) != 200) {
                $result = B2WL_ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
            } else {
                $result = json_decode($request['body'], true);
            }

            return $result;
        }

        public static function php_check()
        {
            return B2WL_ResultBuilder::buildOk();
        }

        public static function php_dom_check()
        {
            if (class_exists('DOMDocument')) {
                return B2WL_ResultBuilder::buildOk();
            } else {
                return B2WL_ResultBuilder::buildError('PHP DOM is disabled');
            }
        }
    }

}

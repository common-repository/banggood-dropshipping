<?php

/**
 * Description of B2WL_Account
 *
 * @author Andrey
 */
if (!class_exists('B2WL_Account')) {

    class B2WL_Account
    {
        private static $_instance = null;

        public $account_type = '';
        public $custom_account = false;

        public $account_data = array('banggood' => array('appkey' => '', 'secretkey' => ''),
            
        );

        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        protected function __construct()
        {
            $this->account_type = b2wl_get_setting('account_type');
            
            $this->account_data = b2wl_get_setting('account_data');
        }

        public function set_account_type($account_type)
        {
            $this->account_type = $account_type;
            b2wl_set_setting('account_type', $this->account_type);
        }
        

        public function get_banggood_account()
        {
            return !empty($this->account_data['banggood']) ? $this->account_data['banggood'] : array('appkey' => '', 'secretkey' => '');
        }

        public function save_banggood_account($appkey, $secretkey)
        {
            $this->account_data['banggood']['appkey'] = $appkey;
            $this->account_data['banggood']['secretkey'] = $secretkey;
            b2wl_set_setting('account_data', $this->account_data);
        }
        

        public function get_access_token()
        {
            $access_token = get_option("b2w_access_token");
            if (false === $access_token || intval($access_token['expires_at']) < time()) {
                try {
                    if (b2wl_check_defined('B2WL_CLIENT_APP_ID')) {
                        $app_id = B2WL_CLIENT_APP_ID;
                    } else {
                        $app_id = isset($this->account_data['banggood']['appkey']) ? $this->account_data['banggood']['appkey'] : "";
                    }

                    if (b2wl_check_defined('B2WL_CLIENT_APP_SECRET')) {
                        $app_secret = B2WL_CLIENT_APP_SECRET;
                    } else {
                        $app_secret = isset($this->account_data['banggood']['secretkey']) ? $this->account_data['banggood']['secretkey'] : "";
                    }

                    if (empty($app_id) || empty($app_secret)) {
                        return B2WL_ResultBuilder::buildError("empty account data");
                    }
                    $request_url = "https://api.banggood.com/getAccessToken?app_id=" . $app_id . "&app_secret=" . $app_secret;
                    $request = b2wl_remote_get($request_url);

                    if (is_wp_error($request)) {
                        return B2WL_ResultBuilder::buildError($request->get_error_message());
                    } else if (intval($request['response']['code']) != 200) {
                        return B2WL_ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
                    }

                    $body = json_decode($request['body'], true);

                    if (intval($body['code']) > 0) {
                        return B2WL_ResultBuilder::buildError(B2WL_Banggood::error_mapping($body['code'], $body['msg']));
                    }

                    $access_token = array('access_token' => $body['access_token'], 'expires_at' => time() + intval($body['expires_in']));

                    delete_option("b2w_access_token");
                    add_option("b2w_access_token", $access_token);
                } catch (Throwable $e) {
                    b2wl_print_throwable($e);
                    return B2WL_ResultBuilder::buildError("build access token failed");
                } catch (Exception $e) {
                    b2wl_print_throwable($e);
                    return B2WL_ResultBuilder::buildError("build access token failed");
                }
            }

            return B2WL_ResultBuilder::buildOk(array('access_token' => $access_token['access_token']));
        }
        
        public function is_activated()
        {
            $item_purchase_code = b2wl_check_defined('B2WL_ITEM_PURCHASE_CODE') ? B2WL_ITEM_PURCHASE_CODE : b2wl_get_setting('item_purchase_code');
            return !empty($item_purchase_code);
        }
    }

}

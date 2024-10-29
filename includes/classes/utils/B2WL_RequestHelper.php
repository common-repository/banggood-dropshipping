<?php

/**
 * Description of B2WL_RequestHelper
 *
 * @author Andrey
 */
if (!class_exists('B2WL_RequestHelper')) {

    class B2WL_RequestHelper
    {
        public static function build_request($function, $params = array())
        {
            $request_url = 'https://api.ali2woo.com/banggood/v1/' . $function . '.php?version=' . B2WL()->version . B2WL_BanggoodLocalizator::getInstance()->build_params();
            

            if (!empty($params) && is_array($params)) {
                foreach ($params as $key => $val) {
                    $request_url .= "&" . str_replace("%7E", "~", rawurlencode($key)) . "=" . str_replace("%7E", "~", rawurlencode($val));
                }
            }

            $request = b2wl_remote_get($request_url);
            if (is_wp_error($request)) {
                return B2WL_ResultBuilder::buildError($request->get_error_message());
            }

            $result = json_decode($request['body'], true);
            if ($result['state'] === 'error') {
                return B2WL_ResultBuilder::buildError($result['message']);
            }

            $result_array = array();

            if (isset($result['dpu'])) {
                $result_array['dpu'] = $result['dpu'];
            }

            $request_url = $result['request'];

            $result = B2WL_Account::getInstance()->get_access_token();
            if ($result['state'] === 'error') {
                return $result;
            }
            $request_url .= '&access_token=' . $result['access_token'];

            $result_array['request_url'] = $request_url;

            return B2WL_ResultBuilder::buildOk($result_array);
        }
    }
}

<?php

/*
Controller Name: Auth
Controller Description: Authentication add-on controller for the Wordpress JSON API plugin
Controller Author: Matt Berg, Ali Qureshi
Controller Author Twitter: @parorrey
 */

class B2WL_JSON_API_Auth_Controller
{

    public function __construct()
    {
        global $b2wl_json_api;
        // allow only connection over https. because, well, you care about your passwords and sniffing.
        // turn this sanity-check off if you feel safe inside your localhost or intranet.
        // send an extra POST parameter: insecure=cool
        if (empty($_SERVER['HTTPS']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off')) {
            if (empty($_REQUEST['insecure']) || $_REQUEST['insecure'] != 'cool') {
                //$b2wl_json_api->error("I'm sorry Dave. I'm afraid I can't do that. (use _https_ please)");
            }
        }
        $allowed_from_post = array('cookie', 'username', 'password', 'seconds', 'nonce');
        foreach ($allowed_from_post as $param) {
            if (isset($_POST[$param])) {
                $b2wl_json_api->query->$param = $_POST[$param];
            }
        }
    }

    public function permissions($method)
    {
        global $b2wl_json_api;
        $protected_methods = array('validate_auth');
        if (in_array($method, $protected_methods)) {
            $b2wl_key = $b2wl_json_api->query->get('b2w-key');
            if (!empty($b2wl_key)) {
                // new auth method
                return $b2wl_json_api->query->is_valid_api_key($b2wl_key);
            } else {
                // old auth method
                if ($b2wl_json_api->query->cookie && wp_validate_auth_cookie($b2wl_json_api->query->cookie, 'logged_in')) {
                    return true;
                }
                return false;
            }
        }
        return true;
    }

    public function validate_auth()
    {
        return array("valid" => true);
    }

    public function validate_auth_cookie()
    {
        if (!$b2wl_json_api->query->cookie) {
            $b2wl_json_api->error("You must include a 'cookie' authentication cookie. Use the `create_auth_cookie` Auth API method.");
        }
        $valid = wp_validate_auth_cookie($b2wl_json_api->query->cookie, 'logged_in') ? true : false;
        return array(
            "valid" => $valid,
        );
    }

    public function generate_auth_cookie()
    {
        global $b2wl_json_api;
        if (!$b2wl_json_api->query->username) {
            $b2wl_json_api->error("You must include a 'username' var in your request.");
        }
        if (!$b2wl_json_api->query->password) {
            $b2wl_json_api->error("You must include a 'password' var in your request.");
        }
        if ($b2wl_json_api->query->seconds) {
            $seconds = (int) $b2wl_json_api->query->seconds;
        } else {
            $seconds = 1209600;
        }
        //14 days
        $user = wp_authenticate($b2wl_json_api->query->username, $b2wl_json_api->query->password);
        if (is_wp_error($user)) {
            $error_messages = array();
            foreach ($user->get_error_codes() as $error_code) {
                $error_messages[] = $user->get_error_message($error_code);
            }
            $b2wl_json_api->error($error_messages ? implode(' # ', $error_messages) : "Invalid username and/or password.", 'error', '401');
            remove_action('wp_login_failed', $b2wl_json_api->query->username);
        }
        $expiration = time() + apply_filters('auth_cookie_expiration', $seconds, $user->ID, true);
        $cookie = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
        preg_match('|src="(.+?)"|', get_avatar($user->ID, 32), $avatar);
        return array(
            "cookie" => $cookie,
            "cookie_name" => LOGGED_IN_COOKIE,
            "user" => array(
                "id" => $user->ID,
                "username" => $user->user_login,
                "nicename" => $user->user_nicename,
                "email" => $user->user_email,
                "url" => $user->user_url,
                "registered" => $user->user_registered,
                "displayname" => $user->display_name,
                "firstname" => $user->user_firstname,
                "lastname" => $user->last_name,
                "nickname" => $user->nickname,
                "description" => $user->user_description,
                "capabilities" => $user->wp_capabilities,
                "avatar" => $avatar[1],
            ),
        );
    }

    public function get_currentuserinfo()
    {
        global $b2wl_json_api;
        if (!$b2wl_json_api->query->cookie) {
            $b2wl_json_api->error("You must include a 'cookie' var in your request. Use the `generate_auth_cookie` Auth API method.");
        }
        $user_id = wp_validate_auth_cookie($b2wl_json_api->query->cookie, 'logged_in');
        if (!$user_id) {
            $b2wl_json_api->error("Invalid authentication cookie. Use the `generate_auth_cookie` Auth API method.");
        }
        $user = get_userdata($user_id);
        preg_match('|src="(.+?)"|', get_avatar($user->ID, 32), $avatar);
        return array(
            "user" => array(
                "id" => $user->ID,
                "username" => $user->user_login,
                "nicename" => $user->user_nicename,
                "email" => $user->user_email,
                "url" => $user->user_url,
                "registered" => $user->user_registered,
                "displayname" => $user->display_name,
                "firstname" => $user->user_firstname,
                "lastname" => $user->last_name,
                "nickname" => $user->nickname,
                "description" => $user->user_description,
                "capabilities" => $user->wp_capabilities,
                "avatar" => $avatar[1],
            ),
        );
    }

}

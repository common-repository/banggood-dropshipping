<?php

if (!class_exists('B2WL_JSON_API_Response')) {

    class B2WL_JSON_API_Response
    {

        public function setup()
        {
            global $b2wl_json_api;
            $this->include_values = array();
            $this->exclude_values = array();
            if ($b2wl_json_api->query->include) {
                $this->include_values = explode(',', $b2wl_json_api->query->include);
            }
            // Props to ikesyo for submitting a fix!
            if ($b2wl_json_api->query->exclude) {
                $this->exclude_values = explode(',', $b2wl_json_api->query->exclude);
                $this->include_values = array_diff($this->include_values, $this->exclude_values);
            }

            // Compatibility with Disqus plugin
            remove_action('loop_end', 'dsq_loop_end');
        }

        public function get_json($data, $status = 'ok')
        {
            global $b2wl_json_api;
            // Include a status value with the response
            if (is_array($data)) {
                $data = array_merge(array('status' => $status), $data);
            } else if (is_object($data)) {
                $data = get_object_vars($data);
                $data = array_merge(array('status' => $status), $data);
            }

            $data = apply_filters('json_api_encode', $data);

            if (function_exists('json_encode')) {
                // Use the built-in json_encode function if it's available
                if (version_compare(PHP_VERSION, '5.3') < 0) {
                    $json = json_encode($data);
                } else {
                    $json_encode_options = 0;
                    if ($b2wl_json_api->query->json_encode_options) {
                        $json_encode_options = $b2wl_json_api->query->json_encode_options;
                    }
                    $json = json_encode($data, $json_encode_options);
                }
            } else {
                // Use PEAR's Services_JSON encoder otherwise
                if (!class_exists('Services_JSON')) {
                    require_once B2WL_JSON_API_DIR . "/library/JSON.php";
                }
                $json_service = new Services_JSON();
                $json = $json_service->encode($data);
            }

            // Thanks to Stack Overflow user Gumbo stackoverflow.com/questions/2934563
            if ($b2wl_json_api->query->json_unescaped_unicode) {
                $callback = array($this, 'replace_unicode_escape_sequence');
                $json = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', $callback, $json);
            }

            return $json;
        }

        public function is_value_included($key)
        {
            // Props to ikesyo for submitting a fix!
            if (empty($this->include_values) && empty($this->exclude_values)) {
                return true;
            } else {
                if (empty($this->exclude_values)) {
                    return in_array($key, $this->include_values);
                } else {
                    return !in_array($key, $this->exclude_values);
                }
            }
        }

        public function respond($result, $status = 'ok', $http_status = 200)
        {
            global $b2wl_json_api;
            $json = $this->get_json($result, $status);
            $status_redirect = "redirect_$status";
            if ($b2wl_json_api->query->dev || !empty($_REQUEST['dev'])) {
                // Output the result in a human-redable format
                if (!headers_sent()) {
                    header('HTTP/1.1 200 OK');
                    header('Content-Type: text/plain; charset: UTF-8', true);
                } else {
                    echo '<pre>';
                }
                echo $this->prettify($json);
            } else if (!empty($_REQUEST[$status_redirect])) {
                wp_redirect($_REQUEST[$status_redirect]);
            } else if ($b2wl_json_api->query->redirect) {
                $url = $this->add_status_query_var($b2wl_json_api->query->redirect, $status);
                wp_redirect($url);
            } else if ($b2wl_json_api->query->callback) {
                // Run a JSONP-style callback with the result
                $this->callback($b2wl_json_api->query->callback, $json);
            } else {
                // Output the result
                $this->output($json, $http_status);
            }
            exit;
        }

        public function output($result, $http_status = 200)
        {
            $http_status = apply_filters('json_api_http_status', $http_status);
            $charset = get_option('blog_charset');
            if (!headers_sent()) {
                status_header($http_status);
                header("Content-Type: application/json; charset=$charset", true);
            }
            echo $result;
        }

        public function callback($callback, $result)
        {
            $charset = get_option('blog_charset');
            if (!headers_sent()) {
                status_header(200);
                header("Content-Type: application/javascript; charset=$charset", true);
            }
            echo "$callback($result)";
        }

        public function add_status_query_var($url, $status)
        {
            if (strpos($url, '#')) {
                // Remove the anchor hash for now
                $pos = strpos($url, '#');
                $anchor = substr($url, $pos);
                $url = substr($url, 0, $pos);
            }
            if (strpos($url, '?')) {
                $url .= "&status=$status";
            } else {
                $url .= "?status=$status";
            }
            if (!empty($anchor)) {
                // Add the anchor hash back in
                $url .= $anchor;
            }
            return $url;
        }

        public function prettify($ugly)
        {
            $pretty = "";
            $indent = "";
            $last = '';
            $pos = 0;
            $level = 0;
            $string = false;
            while ($pos < strlen($ugly)) {
                $char = substr($ugly, $pos++, 1);
                if (!$string) {
                    if ($char == '{' || $char == '[') {
                        if ($char == '[' && substr($ugly, $pos, 1) == ']') {
                            $pretty .= "[]";
                            $pos++;
                        } else if ($char == '{' && substr($ugly, $pos, 1) == '}') {
                            $pretty .= "{}";
                            $pos++;
                        } else {
                            $pretty .= "$char\n";
                            $indent = str_repeat('  ', ++$level);
                            $pretty .= "$indent";
                        }
                    } else if ($char == '}' || $char == ']') {
                        $indent = str_repeat('  ', --$level);
                        if ($last != '}' && $last != ']') {
                            $pretty .= "\n$indent";
                        } else if (substr($pretty, -2, 2) == '  ') {
                            $pretty = substr($pretty, 0, -2);
                        }
                        $pretty .= $char;
                        if (substr($ugly, $pos, 1) == ',') {
                            $pretty .= ",";
                            $last = ',';
                            $pos++;
                        }
                        $pretty .= "\n$indent";
                    } else if ($char == ':') {
                        $pretty .= ": ";
                    } else if ($char == ',') {
                        $pretty .= ",\n$indent";
                    } else if ($char == '"') {
                        $pretty .= '"';
                        $string = true;
                    } else {
                        $pretty .= $char;
                    }
                } else {
                    if ($last != '\\' && $char == '"') {
                        $string = false;
                    }
                    $pretty .= $char;
                }
                $last = $char;
            }
            return $pretty;
        }

        public function replace_unicode_escape_sequence($match)
        {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }

    }

}

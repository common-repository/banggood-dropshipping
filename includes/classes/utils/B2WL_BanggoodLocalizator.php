<?php

/**
 * Description of B2WL_BanggoodLocalizator
 *
 * @author Andrey
 */
if (!class_exists('B2WL_BanggoodLocalizator')) {

    class B2WL_BanggoodLocalizator {

        private static $_instance = null;
        public $language;
        public $currency;
        public $all_currencies = array();

        protected function __construct() {
            $this->language = strtolower(b2wl_get_setting('import_language'));
            $this->currency = strtoupper(b2wl_get_setting('local_currency'));

            $currencies_file = B2WL()->plugin_path() . '/assets/data/currencies.json';  
            if(file_exists ($currencies_file)){
                $this->all_currencies = json_decode(file_get_contents($currencies_file), true);
            }

            if (b2wl_check_defined('B2WL_CUSTOM_CURRENCY')) {
                $cca = explode(";", B2WL_CUSTOM_CURRENCY);
                if (is_array($cca)) {
                    foreach ($cca as $cc) {
                        if($cc) {
                            $tmp_cur=explode("#", $cc);
                            if(isset($tmp_cur[0])){
                                $this->all_currencies[] =array('code'=>$tmp_cur[0], 'name'=>isset($tmp_cur[1])?$tmp_cur[1]:$tmp_cur[0], 'custom'=>true);
                            }
                        }
                    }
                }
            }
        }

        protected function __clone() {
            
        }

        static public function getInstance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function getLocaleCurr($in_curr=false) {
            $out_curr = $in_curr?$in_curr:$this->currency;
            if ($out_curr == 'USD') {
                return '$';
            }
            return $out_curr . ' ';
        }

        public function isCustomCurrency($in_curr=false){
            $custom_currencies =  $this->getCurrencies(true);
            return isset($custom_currencies[$in_curr?strtoupper($in_curr):$this->currency]);
        }

        public function getCurrencies($custom = false) {  
            $result = array();

            foreach($this->all_currencies as $c){
                if(!$custom && !$c['custom']){
                    $result[strtoupper($c['code'])] = $c['name'];
                } else if($custom && $c['custom']){
                    $result[strtoupper($c['code'])] = $c['name'];
                }
            }
            return $result;
        }

        public function build_params() {
            return '&lang=' . $this->language . '&currency=' . $this->currency;
        }

    }

}

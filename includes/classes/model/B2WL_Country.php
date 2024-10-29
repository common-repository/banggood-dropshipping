<?php

/**
 * Description of B2WL_Country
 *
 * @author Andrey
 */
if (!class_exists('B2WL_Country')) {

    class B2WL_Country
    {
        private $countries = array();

        public function get_countries()
        {
            if (empty($this->countries)) {
                $this->countries = json_decode(file_get_contents(B2WL()->plugin_path() . '/assets/data/countries.json'), true);
                $this->countries = $this->countries["countries"];
                array_unshift($this->countries, array('c' => '', 'n' => 'N/A', 'wc' => ''));
            }
            return $this->countries;
        }

        public function get_country($code)
        {
            $countries = $this->get_countries();
            foreach ($countries as $c) {
                if ($c['c'] === strtoupper($code)) {
                    return $c;
                    break;
                }
            }
            return false;
        }

        public function get_warehouse_code_by_country($code)
        {
            $countries = $this->get_countries();
            foreach ($countries as $c) {
                //for most countries warehouse code is the same as country code, but there is exclusions, such as: us (usa), etc.
                if ((isset($c['wc']) && $c['wc'] === strtolower($code)) || $c['c'] === strtoupper($code)) {

                    return isset($c['wc']) ? $c['wc'] : $c['c'];

                    break;
                }
            }
            return false;
        }

    }

}

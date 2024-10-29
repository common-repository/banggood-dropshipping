<?php

/**
 * Description of B2WL_BanggoodAccount
 *
 * @author Andrey
 */
if (!class_exists('B2WL_BanggoodAccount')) {

    class B2WL_BanggoodAccount extends B2WL_AbstractAccount
    {
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function getDeeplink($hrefs, $dpu)
        {
            $result = array();
            
            if ($hrefs) {
                if (!empty($dpu)) {
                    $hrefs = is_array($hrefs) ? array_values($hrefs) : array(strval($hrefs));
                    foreach ($hrefs as $href) {
                        $href2 = $this->getNormalizedLink($href);

                        if (parse_url($dpu, PHP_URL_QUERY)) {
                            $cashback_url = $dpu . '&ulp=' . urlencode($href2);
                        } else {
                            $cashback_url = $dpu . '?ulp=' . urlencode($href2);
                        }

                        $result[] = array('url' => $href, 'promotionUrl' => $cashback_url);
                    }
                }
            }
            
            return $result;
        }
    }

}

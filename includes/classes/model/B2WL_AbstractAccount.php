<?php

/* * class
 * Description of B2WL_AbstractAccount
 *
 * @author Andrey
 *
 * @position: 1
 */

if (!class_exists('B2WL_AbstractAccount')) {

    abstract class B2WL_AbstractAccount
    {
        protected static $_instance = null;

        protected function __construct()
        {}

        abstract public static function getInstance();

        abstract public function getDeeplink($hrefs, $dpu);

        protected function getNormalizedLink($href)
        {
            preg_match('/([0-9]+)\.html/', $href, $match);
            $ext_id = $match[1];
            $href = str_replace("{$ext_id}/", "", $href);

            return $href;
        }
    }
}

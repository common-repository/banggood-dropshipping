<?php

/**
 * Description of B2WL_SearchPage
 *
 * @author andrey
 *
 * @autoload: b2wl_admin_init
 */
if (!class_exists('B2WL_SearchPageController')) {

    class B2WL_SearchPageController extends B2WL_AbstractAdminPage
    {

        private $loader;

        public function __construct()
        {
            parent::__construct(__('Search Products', 'bng2woo-lite'), __('Search Products', 'bng2woo-lite'), 'import', 'b2wl_dashboard', 10);

            $this->loader = new B2WL_Banggood();
            $this->localizator = B2WL_BanggoodLocalizator::getInstance();

        }

        public function render($params = array())
        {
            $filter = array();
            if (is_array($_GET) && $_GET) {
                $filter = array_merge($filter, $_GET);
                if (isset($filter['cur_page'])) {
                    unset($filter['cur_page']);
                }
                if (isset($filter['page'])) {
                    unset($filter['page']);
                }
            }

            unset($filter['search']);
            foreach ($filter as $key => $val) {
                $new_key = preg_replace('/b2wl_/', '', $key, 1);
                unset($filter[$key]);
                $filter[$new_key] = wp_unslash($val);
            }

            $page = isset($_GET['cur_page']) && intval($_GET['cur_page']) ? intval($_GET['cur_page']) : 1;
            $per_page = 20;

            if (!empty($filter['category_id'])) {
                $load_products_result = $this->loader->load_products($filter, $page, $per_page);
            } else {
                $load_products_result = B2WL_ResultBuilder::buildError(__("Please choose a Banggood's category to make a search.", 'bng2woo-lite'));
            }

            if ($load_products_result['state'] == 'error' || $load_products_result['state'] == 'warn') {
                add_settings_error('b2wl_products_list', esc_attr('settings_updated'), $load_products_result['message'], 'error');
            }

            if ($load_products_result['state'] != 'error') {
                $pages_list = array();
                $links = 4;
                $last = ceil($load_products_result['total'] / $per_page);
                $load_products_result['total_pages'] = $last;
                $start = (($load_products_result['page'] - $links) > 0) ? $load_products_result['page'] - $links : 1;
                $end = (($load_products_result['page'] + $links) < $last) ? $load_products_result['page'] + $links : $last;
                if ($start > 1) {
                    $pages_list[] = 1;
                    $pages_list[] = '';
                }
                for ($i = $start; $i <= $end; $i++) {
                    $pages_list[] = $i;
                }
                if ($end < $last) {
                    $pages_list[] = '';
                    $pages_list[] = $last;
                }
                $load_products_result['pages_list'] = $pages_list;

                b2wl_set_transient('b2wl_search_result', $load_products_result['products']);
            }

            $countryModel = new B2WL_Country();

            $this->model_put('filter', $filter);
            $this->model_put('categories', $this->get_categories());

            $this->model_put('load_products_result', $load_products_result);

            $this->include_view('search.php');
        }

        protected function get_categories()
        {
            if (file_exists(B2WL()->plugin_path() . '/assets/data/categories.json')) {
                $categories = json_decode(file_get_contents(B2WL()->plugin_path() . '/assets/data/categories.json'), true);
            } else {
                $categories = array();
            }

            function build_category_tree($in_categories, $parent_id = 0, $level = 0)
            {
                $cats = array();
                foreach ($in_categories as $cat) {
                    if ($cat['parent_id'] == $parent_id) {
                        $cat['level'] = $level;
                        $cats[] = $cat;
                    }
                }
                foreach ($cats as &$cat) {
                    $sub_categories = build_category_tree($in_categories, $cat['cat_id'], $level + 1);
                    if (!empty($sub_categories)) {
                        $cat['sub_categories'] = $sub_categories;
                    }

                }
                return $cats;
            }

            function flat_category($in_categories)
            {
                $cats = array();
                foreach ($in_categories as $cat) {
                    $sub_categories = array();
                    if (isset($cat['sub_categories'])) {
                        $sub_categories = flat_category($cat['sub_categories']);
                        unset($cat['sub_categories']);

                    }
                    $cats = array_merge($cats, array($cat), $sub_categories);
                }
                return $cats;
            }

            $categories = flat_category(build_category_tree($categories));

            $categories = array_merge(array(array("cat_id" => "0", "cat_name" => "Select category", "parent_id" => 0, "level" => 0)), $categories);
            return $categories;
        }

    }

}

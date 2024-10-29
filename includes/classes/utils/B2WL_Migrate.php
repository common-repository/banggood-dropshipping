<?php

/**
 * Description of B2WL_Migrate
 *
 * @author Andrey
 *
 * @autoload: b2wl_admin_init
 */

if (!class_exists('B2WL_Migrate')) {

    class B2WL_Migrate
    {
        public function __construct()
        {
            $this->migrate();
        }

        public function migrate()
        {
            $cur_version = get_option('b2wl_db_version', '');
            if (version_compare($cur_version, "1.1.0", '<')) {
                $this->migrate_to_110();
            }

            if (version_compare($cur_version, B2WL()->version, '<')) {
                update_option('b2wl_db_version', B2WL()->version, 'no');
            }
        }

        private function migrate_to_110()
        {
            b2wl_error_log('migrate to 1.1.0');

            $import = new B2WL_ProductImport();
            $product_ids = $import->get_product_id_list();
            foreach ($product_ids as $product_id) {
                if ($product = $import->get_product($product_id)) {
                    foreach ($product['tmp_edit_images'] as $edit_image) {
                        if (isset($edit_image['attachment_id'])) {
                            B2WL_Utils::delete_attachment($edit_image['attachment_id'], true);
                        }
                    }
                }
            }
            $import->del_product($product_ids);
        }
    }
}

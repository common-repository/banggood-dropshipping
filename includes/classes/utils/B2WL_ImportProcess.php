<?php

/**
 * Description of B2WL_ImportProcess
 *
 * @author Andrey
 * 
 */
if (!class_exists('B2WL_ImportProcess')) {


    class B2WL_ImportProcess extends WP_Background_Process {
        
        protected $action = 'b2wl_import_process';

        public function __construct() {
            parent::__construct();
        }

        /**
         * Task
         *
         * @param mixed $item Queue item to iterate over
         *
         * @return mixed
         */
        protected function task( $item ) {
            b2wl_init_error_handler();
            try {
                $woocommerce_model = new B2WL_Woocommerce();
                $product_import_model = new B2WL_ProductImport();

                $ts = microtime(true);
                b2wl_info_log("START_STEP[id:".$item['product_id'].", extId: ".$item['id'].", step: ".$item['step']."]");

                $product = $product_import_model->get_product($item['id'], true);
                if ($product) {
                    $result = $woocommerce_model->add_product($product, $item);

                    if(!empty($result['new_steps'])){
                        // add new steps to new queue
                        B2WL_ImportProcess::create_new_queue($item['product_id'], $item['id'], $result['new_steps']);
                    }

                    if(
                        // (!b2wl_get_setting('use_external_image_urls') && substr($item['step'], 0, strlen('preload_images')) === 'preload_images') || 
                        $item['step']=='finishing'
                    ){
                        add_filter($this->identifier . '_time_exceeded', array($this, 'finish_iteration'));
                    }
                    
                    if ($result['state'] === 'error') {
                        throw new Exception($result['message']);
                    }
                } else {
                    throw new Exception('product not found in import list');
                }    

                b2wl_info_log("DONE_STEP[time: ".(microtime(true)-$ts).", id:".$item['product_id'].", extId: ".$item['id'].", step: ".$item['step']."]");
                
            } catch (Throwable $e) {
                b2wl_print_throwable($e);
            } catch (Exception $e) {
                b2wl_print_throwable($e);
            }

            return false;
        }

        public function finish_iteration($res) {
            return true;
        } 

        public static function init() {
            new B2WL_ImportProcess();
        }

        public static function create_new_queue($product_id, $external_id, $steps, $start = true) {
            $new_queue = new B2WL_ImportProcess();
            foreach($steps as $step) {
                $new_queue->push_to_queue(array('id'=>$external_id, 'step'=>$step, 'product_id'=>$product_id));
                b2wl_info_log("ADD_STEP[id:".$product_id.", extId: ".$external_id.", step: ".$step."]");
            }
            $new_queue->save();
            if($start){
                $new_queue->dispatch();
            }
            return $new_queue;
        }

        public function num_in_queue() {
			global $wpdb;

			$table  = $wpdb->options;
			$column = 'option_name';

			if ( is_multisite() ) {
				$table  = $wpdb->sitemeta;
				$column = 'meta_key';
			}

			$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

			$count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM {$table}
			WHERE {$column} LIKE %s
		    ", $key ) );

			return $count;
		}

        public function clean_queue() {
			global $wpdb;

			$table        = $wpdb->options;
			$column       = 'option_name';
			$key_column   = 'option_id';
			$value_column = 'option_value';

			if ( is_multisite() ) {
				$table        = $wpdb->sitemeta;
				$column       = 'meta_key';
				$key_column   = 'meta_id';
				$value_column = 'meta_value';
			}

			$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

			$query = $wpdb->get_results( $wpdb->prepare( "
			SELECT *
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$key_column} ASC
		    ", $key ) );

            foreach ( $query as $row ) {
                $this->delete( $row->$column );
            }
		}
    }
}

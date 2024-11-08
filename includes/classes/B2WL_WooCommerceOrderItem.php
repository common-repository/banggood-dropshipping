<?php

/**
 * Description of B2WL_WooCommerceOrderItem
 *
 * @author MA_GROUP
 */
if (!class_exists('B2WL_WooCommerceOrderItem')):

	class B2WL_WooCommerceOrderItem {
		private $orderItem;
		
		function __construct($order_item){
			$this->orderItem  = $order_item;
		}
		
		public function getName(){
			if (is_array($this->orderItem)) return $this->orderItem['name'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_name();  
		}
		
		public function getProductID(){
			if (is_array($this->orderItem)) return $this->orderItem['product_id'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_product_id();   
		}
		
		public function getVariationID(){
			if (is_array($this->orderItem)) return $this->orderItem['variation_id'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_variation_id();   
		}
		
		public function getQuantity(){
			if (is_array($this->orderItem)) return $this->orderItem['qty'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_quantity();     
		}

        public function get_B2WL_ShippingTitle()
        {
            $shipping_title= '';

            if (isset($this->orderItem['item_meta']['Shipping'])) {

                if (is_array($this->orderItem['item_meta']['Shipping'])) {
                    $shipping_title = $this->orderItem['item_meta']['Shipping'][0];
                } else {
                    $shipping_title = $this->orderItem['item_meta']['Shipping'];
                }
            }

            return $shipping_title;
        }

        public function get_B2WL_ShippingCode()
        {
            $shipping_code= '';

            if (isset($this->orderItem['item_meta']['b2wl_shipping_code'])) {

                if (is_array($this->orderItem['item_meta']['b2wl_shipping_code'])) {
                    $shipping_code = $this->orderItem['item_meta']['b2wl_shipping_code'][0];
                } else {
                    $shipping_code = $this->orderItem['item_meta']['b2wl_shipping_code'];
                }
            }

            return $shipping_code;
        }

		
	}


endif;


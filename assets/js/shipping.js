jQuery(document).ready(function($){
    
    var b2wl_shipping_api = {
         init_in_cart : function(){},
         init_shipping_dropdown_in_cart : function(){}   
    };


   b2wl_shipping_api.init_in_cart = function(t){
        $('.b2wl_shipping').html(t);   
        
        //b2w block:
        $('#b2wl_to_country_field').on('change', function() {
            $('#calc_shipping_country').val(this.value);
            $('<input type="hidden" name="b2wl_skip_postcode_validate" value="1"/>').appendTo('.woocommerce-shipping-calculator');
            $('.woocommerce-shipping-calculator').submit();
            
            //ajax_update_shipping_and_totals(this.value);           
        }); 
        
        //default woocommerce block:
        $( document.body ).on(
        'updated_wc_div',
        function(){ 
            var ajax_country = $('#calc_shipping_country').val();
            $('#b2wl_to_country_field').val( ajax_country ) ;  
        });

        //update country on page loads
       $('#b2wl_to_country_field').trigger('change');
   }
   
   b2wl_shipping_api.init_shipping_dropdown_in_cart = function(i){
       // $('.b2wl_shipping_field'+i+'_container').html(t);   
     
          $('body').on('change', '#b2wl_shipping_field'+i, function(e) {
              $('<input type="hidden" name="b2wl_skip_postcode_validate" value="1"/>').appendTo('.woocommerce-shipping-calculator');
              var country = '';

              //wc cart page
              if ( $('#b2wl_to_country_field').length > 0 ) country = $('#b2wl_to_country_field').val() ;
              //wc checkout page
              else {
                  if ($('.shipping_address').length > 0 && $('.shipping_address').css('display') == "none" ){
                      country = $('#billing_country').val();
                  } else {
                    country = $('#shipping_country').val();  
                  }
              }

              ajax_update_shipping_method_in_cart_item(i, this.value,  country);           
          }); 
   }	
   
   ajax_update_shipping_method_in_cart_item = function(id, tariff_code, country){
        var data = {'action': 'b2wl_update_shipping_method_in_cart_item','id':id, 'value': tariff_code, 'calc_shipping_country' : country};
       
           $.ajax({
               url : b2wl_ali_ship_data.ajaxurl,
               type : 'POST',
               data : data,
               tryCount : 0,
               retryLimit : 3, 
               success : function (response) {
              
                   if (response == ''){
                        this.tryCount++;
                        if (this.tryCount <= this.retryLimit) {
                            //try again
                            $.ajax(this);
                            return;
                        }
                        console.log('Something is wrong with your server');            
                        return;     
                   }
                                              
                    var json = jQuery.parseJSON(response);
                    
                    if (json.state){
                   
                        if (json.state == "ok"){
                            //for cart
                            if ($('.woocommerce-shipping-calculator').length > 0)
                                $('.woocommerce-shipping-calculator').submit(); 
                            else {
                                //for checkout
                                $( document.body ).trigger( 'update_checkout' );                           
                            }                                                       
                        }
                        
                        if (json.state == "error"){
                            //just reserved for error 
                        }
                
                    
                    }
                
               },                
               error : function(xhr, textStatus, errorThrown ) {
                    if (textStatus == 'timeout') {
                        this.tryCount++;
                        if (this.tryCount <= this.retryLimit) {
                            //try again
                            $.ajax(this);
                            return;
                        }            
                        return;
                    }
                    if (xhr.status == 500) {
                        //handle error
                    } else {
                        //handle error
                    }
               }
          });    
   }

   $( "body" ).trigger( 'b2wl_shipping_js_loaded',[b2wl_shipping_api]);
      
})

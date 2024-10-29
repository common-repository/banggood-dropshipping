<?php
$b2w_local_currency = strtoupper(b2wl_get_setting('local_currency'));
?>
<form method="post" enctype='multipart/form-data'>
    <input type="hidden" name="setting_form" value="1"/>
    <div class="panel panel-primary mt20">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo esc_html_x('Shipping settings', 'Setting title', 'bng2woo-lite'); ?></h3>
            <span class="pull-right">
                <a href="#" class="reset-shipping-meta btn _b2wfv"><?php echo esc_html_x('Reset product shipping meta', 'Setting title', 'bng2woo-lite'); ?><div class="info-box" data-placement="left" data-toggle="tooltip" title="<?php echo esc_html_x('It clears the shipping methods cache, use this feature if you believe the shipping cost is changed on Banggood.', 'Setting tip', 'bng2woo-lite'); ?>"></div></a>
            </span>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <label>
                        <strong><?php echo esc_html_x('Default shipping class', 'Setting title', 'bng2woo-lite'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('Specific shipping class for WooCommerce, that get all products imported via Bng2Woo Lite.', 'setting description', 'bng2woo-lite'); ?>"></div>
                </div>
                <div class="col-md-8">
                    <div class="form-group input-block no-margin">
                        <?php $default_shipping_class = b2wl_get_setting('default_shipping_class');?>
                        <select name="b2wl_default_shipping_class" id="b2wl_default_shipping_class" class="form-control small-input">
                            <option value=""><?php echo esc_html_x('Do nothing', 'Setting option', 'bng2woo-lite'); ?></option>
                            <?php foreach ($shipping_class as $sc): ?>
                            <option value="<?php echo $sc->term_id; ?>" <?php if ($default_shipping_class == $sc->term_id): ?>selected="selected"<?php endif;?>><?php echo $sc->name; ?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="_b2wfo b2wl-info"><div>This feature is available in the full plugin version.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
            <div class="row _b2wfv">
                <div class="col-md-4">
                    <label>
                        <strong><?php echo esc_html_x('Default Shipping Country', 'Setting title', 'bng2woo-lite'); ?></strong>
                    </label>
                    <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('This is for the frontend (Cart, Checkout, Product page) and for the backend Bng2Woo Lite`s pages (Search, Import List, etc.).', 'setting description', 'bng2woo-lite'); ?>"></div>
                </div>
                <div class="col-md-8">
                    <?php $cur_b2w_aliship_shipto = b2wl_get_setting('aliship_shipto');?>
                    <div class="form-group input-block no-margin">
                        <select name="b2w_aliship_shipto" id="b2w_aliship_shipto" class="form-control small-input country_list">
                            <?php foreach ($shipping_countries as $country): ?>
                                <option value="<?php echo $country['c']; ?>"<?php if ($cur_b2w_aliship_shipto == $country['c']): ?> selected<?php endif;?>>
                                    <?php echo $country['n']; ?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default mt20">
        <div class="_b2wfo b2wl-info"><div>This feature is available in the full plugin version.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
        <div class="panel-body _b2wfv">
            <div class="global-pricing mt20">
                <div class="panel panel-primary mt20 _b2wfv">
                    <div class="panel-heading">
                        <h3 class="display-inline"><?php echo esc_html_x('Global shipping rules', 'Setting title', 'bng2woo-lite'); ?><div class="info-box" data-placement="left" data-toggle="tooltip" title="<?php echo esc_html_x('Please note that you can disable Global rules for specific shipping methods if needed. Just go to "Shipping List" page, then choose "specific method" and set  "Enable price rule" to "no".', 'Setting tip', 'bng2woo-lite'); ?>"></div></h3>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row pt20">
            <div class="col-sm-12">
                <input class="btn btn-success" type="submit" value="<?php esc_html_e('Save settings', 'bng2woo-lite');?>"/>
            </div>
        </div>
    </div>

</form>

<script>
    function b2wl_isInt(value) {
        return !isNaN(value) &&
                parseInt(Number(value)) == value &&
                !isNaN(parseInt(value, 10));
    }

    (function ($) {

        $('.b2wl-placeholder-value').on('click', function () {
            $(this).select();
        });
        $('.b2wl-placeholder-value-copy').on('click', function () {
            let $container = $(this).closest('.b2wl-placeholder-value-container');
            $container.find('.b2wl-placeholder-value').select();
            document.execCommand('copy');
        });

        $(".reset-shipping-meta").on("click", function () {
            if(!$(".reset-shipping-meta").hasClass('processing')){
                $(".reset-shipping-meta").addClass('processing');
                var data = {'action': 'b2wl_reset_shipping_meta'};
                jQuery.post(ajaxurl, data).done(function (response) {
                    $(".reset-shipping-meta").removeClass('processing');
                    var json = jQuery.parseJSON(response);
                    if(json.state==='ok'){
                        show_notification('Reset product shipping meta Done');
                    }else{
                        show_notification(json.message, true);
                    }
                }).fail(function (xhr, status, error) {
                    $(".reset-shipping-meta").removeClass('processing');
                    show_notification('Applying pricing rules failed.', true);
                });
            }

            return false;
        });

        function get_el_sign_value(el) {
            return el.children('li')
                    .filter(function () {
                        return $(this).css('display') === 'none'
                    })
                    .attr('data-sign');
        }

        function get_value(compared) {
            var s_class = 'compared_value';
            if (typeof compared == "undefined")
                s_class = 'value';

            return $('.js-default-prices .' + s_class).val();
        }

        function rule_info_box_calculation(str_tmpl, sign, value) {

            var def_value = 1, result = value;
            if (sign == "+")
                result = def_value + Number(value);
            if (sign == "*")
                result = def_value * Number(value);

            return sprintf(str_tmpl, def_value, result, def_value, sign, value, result)

        }

        if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }

        //info content
        $(".js-default-prices div.info-box").on("mouseover", function () {
            $(this).attr('title', rule_info_box_calculation("E.g., A product shipping that costs %d <?php echo $b2w_local_currency; ?> would have its price set to %d <?php echo $b2w_local_currency; ?> (%d %s %d = %d).", get_el_sign_value($('.js-default-prices ul.sign')), get_value()));
            if(jQuery.fn.tooltip) { $(this).tooltip('fixTitle').tooltip('show'); }
        });



        //default rule dropdown
        $(".global-pricing .dropdown").on("click", function () {
            $(this).next().slideToggle();
        });
        $(".global-pricing .dropdown-menu li").click("click", function (e) {
            e.preventDefault();
            $(this).trigger('change');
            var sign = $(this).attr('data-sign'),
                    svg = $(this).closest('.input-group').prev('svg'),
                    svg = svg.length > 0 ? svg : $(this).closest('td').prev('td').find("svg"),
                    svg = svg.length > 0 ? svg : $(this).closest('.row').find('svg.sign');

            $('input[name="default_rule[sign]"]').val(sign);

            if (sign == '=') {
                svg.removeClass('icon-equal icon-plus icon-rotate45').addClass('icon-equal');
                svg.children('use').attr('xlink:href', '#icon-equal');
            }
            else if (sign == '*') {
                svg.removeClass('icon-equal icon-plus icon-rotate45').addClass('icon-plus icon-rotate45');
                svg.children('use').attr('xlink:href', '#icon-plus');
            }
            else if (sign == '+') {
                svg.removeClass('icon-equal icon-plus icon-rotate45').addClass('icon-plus');
                svg.children('use').attr('xlink:href', '#icon-plus');
            }

            $(this).hide().siblings().each(function () {
                $(this).show()
            });
            $(this).parent().fadeOut().prev().html($(this).text());
        });

        $('.b2wl-content form').on('submit', function () {

            if ($(this).find('.has-error').length > 0)
                return false;
        });

    })(jQuery);




</script>

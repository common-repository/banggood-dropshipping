// Create Base64 Object
var Base64 = { _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) { var t = ""; var n, r, i, s, o, u, a; var f = 0; e = Base64._utf8_encode(e); while (f < e.length) { n = e.charCodeAt(f++); r = e.charCodeAt(f++); i = e.charCodeAt(f++); s = n >> 2; o = (n & 3) << 4 | r >> 4; u = (r & 15) << 2 | i >> 6; a = i & 63; if (isNaN(r)) { u = a = 64 } else if (isNaN(i)) { a = 64 } t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a) } return t }, decode: function (e) { var t = ""; var n, r, i; var s, o, u, a; var f = 0; e = e.replace(/[^A-Za-z0-9\+\/\=]/g, ""); while (f < e.length) { s = this._keyStr.indexOf(e.charAt(f++)); o = this._keyStr.indexOf(e.charAt(f++)); u = this._keyStr.indexOf(e.charAt(f++)); a = this._keyStr.indexOf(e.charAt(f++)); n = s << 2 | o >> 4; r = (o & 15) << 4 | u >> 2; i = (u & 3) << 6 | a; t = t + String.fromCharCode(n); if (u != 64) { t = t + String.fromCharCode(r) } if (a != 64) { t = t + String.fromCharCode(i) } } t = Base64._utf8_decode(t); return t }, _utf8_encode: function (e) { e = e.replace(/\r\n/g, "\n"); var t = ""; for (var n = 0; n < e.length; n++) { var r = e.charCodeAt(n); if (r < 128) { t += String.fromCharCode(r) } else if (r > 127 && r < 2048) { t += String.fromCharCode(r >> 6 | 192); t += String.fromCharCode(r & 63 | 128) } else { t += String.fromCharCode(r >> 12 | 224); t += String.fromCharCode(r >> 6 & 63 | 128); t += String.fromCharCode(r & 63 | 128) } } return t }, _utf8_decode: function (e) { var t = ""; var n = 0; var r = c1 = c2 = 0; while (n < e.length) { r = e.charCodeAt(n); if (r < 128) { t += String.fromCharCode(r); n++ } else if (r > 191 && r < 224) { c2 = e.charCodeAt(n + 1); t += String.fromCharCode((r & 31) << 6 | c2 & 63); n += 2 } else { c2 = e.charCodeAt(n + 1); c3 = e.charCodeAt(n + 2); t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63); n += 3 } } return t } }

var b2wl_chrome_extension_loaded = false;
document.addEventListener('b2wExtensionLoaded', function () {
    jQuery("#chrome-notify").hide();
    b2wl_chrome_extension_loaded = true;
});

var waitForFinalEvent = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout(timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

function b2wl_tmce_getContent(editor_id, textarea_id) {
    if (typeof editor_id == 'undefined') editor_id = wpActiveEditor;
    if (typeof textarea_id == 'undefined') textarea_id = editor_id;

    if (jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id)) {
        return tinyMCE.get(editor_id).getContent();
    } else {
        return jQuery('#' + textarea_id).val();
    }
}

function show_notification(text, danger) {

    if (jQuery(".b2wl-content .alert").length === 0) {
        jQuery('<div class="alert" role="alert">').prependTo(jQuery(".b2wl-content"));
    }

    var alert = jQuery(".b2wl-content .alert");

    alert.removeClass('alert-success alert-danger');

    alert.addClass((typeof danger !== "undefined") ? 'alert-danger' : 'alert-success')

    alert.html(text);
    alert.fadeTo(5000, 500, function () {
        alert.fadeOut("slow");
    });
}

function b2wl_need_update_product(id) {
    return Object.keys(jQuery('[data-id="' + id + '"]').data('update') || {}).length > 0
}

function b2wl_update_product(id, data, on_update_calback, timeout = 2000) {
    let prevData = jQuery('[data-id="' + id + '"]').data('update');
    let newData = { ...prevData, ...data };
    jQuery('[data-id="' + id + '"]').data('update', newData);

    let updateFn = function (curData) {
        jQuery.post(ajaxurl, { ...curData, action: 'b2wl_update_product_info', id }).done(function (response) {
            var json = jQuery.parseJSON(response);
            if (json.state !== 'ok') {
                console.log(json);
            }

            let pd = jQuery('[data-id="' + id + '"]').data('update');
            let npd = Object.keys(pd).reduce(function (acc, key) {
                return curData[key] === undefined ? { ...acc, [key]: pd[key] } : acc;
            }, {});
            jQuery('[data-id="' + id + '"]').data('update', npd);

            if (on_update_calback) {
                on_update_calback(json);
            }
        }).fail(function (xhr, status, error) {
            console.log(error);

            if (on_update_calback) {
                on_update_calback({ state: "error" });
            }
        });
    }

    waitForFinalEvent(function () { updateFn(newData); }, timeout, "update_product_" + id);

}

function chech_products_view() {
    var products_to_load = [];
    jQuery(".product-card .product-card-shipping-info").each(function () {
        var _this_data = jQuery(this).data();
        if (_this_data && !_this_data.shipping) {
            if (Utils.isElementInView(jQuery(this).closest(".product-card"), false)) {
                products_to_load.push(jQuery(this).closest(".product-card").attr('data-id'));
                _this_data.shipping = 'loading';
                jQuery(this).data(_this_data);
            }
        }
    });

    if (products_to_load.length > 0) {
        var data = { 'action': 'b2wl_load_shipping_info', 'id': products_to_load };
        jQuery.post(ajaxurl, data).done(function (response) {
            var json = jQuery.parseJSON(response);
            if (json.state !== 'ok') {
                console.log(json);
            }
            if (json.state != 'error') {
                jQuery.each(json.products, function (i, product) {
                    const product_block = jQuery('.product-card[data-id="' + product.product_id + '"] .product-card-shipping-info');
                    const tmp_data = jQuery(product_block).data();
                    tmp_data.shipping = product.items;
                    jQuery(product_block).data(tmp_data);
                    var p = -1;
                    var fp = '';
                    var n = '';
                    var t = '';
                    jQuery.each(product.items, function (j, item) {
                        const price = item.shipfee;
                        if (p < 0 || price < p) {
                            p = price;
                            fp = p > 0.009 ? item.shipfeeFormatStr : 'Free';
                            n = item.shipmethod_name;
                            t = item.shipday + ' days';
                        }
                    });
                    jQuery(product_block).find('.shipping-title').html(fp + ' ' + n);
                    jQuery(product_block).find('.delivery-time').html(t);

                });
            }
        }).fail(function (xhr, status, error) {
            console.log(error);
        });
    }

}

function find_min_shipping_price(items, default_method) {
    var result = false;
    var p = -1;
    jQuery.each(items, function (i, item) {
        const price = item.shipfee;
        if (p < 0 || price < p || item.shipmethod_code == default_method) {
            p = price;
            result = { 'shipmethod_code': item.shipmethod_code, 'price': price, 'formated_price': price > 0.009 ? item.shipfeeFormatStr : 'Free', 'name': item.shipmethod_name, 'time': item.shipday };
            if (item.shipmethod_code == default_method) {
                return false;
            }
        }
    });
    return result;
}

function fill_modal_shipping_info(product_id, country_from_list, country_from, country_to, items, page = 'search', default_method = '', onSelectCallback = null) {
    const tmp_data = { product_id, country_from_list, country_from, country_to, 'shipping': items, page, default_method, onSelectCallback };
    jQuery(".modal-shipping").data(tmp_data);

    jQuery('#b2wl-modal-country-from-select').html('')
    if (country_from_list.length > 0) {
        jQuery(".modal-shipping").addClass('with-country-from')
        jQuery.each(country_from_list, function (i, c) {
            jQuery('#b2wl-modal-country-from-select').append('<option value="' + c + '">' + c + '</option>')
        });
        jQuery('#b2wl-modal-country-from-select').val(country_from).trigger('change');
    } else {
        jQuery(".modal-shipping").removeClass('with-country-from')
    }

    jQuery('#b2wl-modal-country-select').val(country_to).trigger('change');

    const min_shipping_price = find_min_shipping_price(tmp_data.shipping, default_method);

    let html = '<table class="shipping-table"><thead><tr><th></th><th><strong>Shipping Method</strong></th><th><strong>Estimated Delivery Time</strong></th><th><strong>Shipping Cost</strong></th></tr></thead><tbody>';
    jQuery.each(tmp_data.shipping, function (i, item) {
        html += '<tr><td><input type="radio" class="select_method" value="' + item.shipmethod_code + '" name="p-' + product_id + '" id="' + product_id + '-' + item.shipmethod_code + '" ' + (min_shipping_price && item.shipmethod_code == min_shipping_price.shipmethod_code ? 'checked="checked"' : '') + '></td><td><label for="' + product_id + '-' + item.shipmethod_code + '">' + item.shipmethod_name + '</label></td><td><label for="' + product_id + '-' + item.shipmethod_code + '">' + item.shipday + '</label></td><td><label for="' + product_id + '-' + item.shipmethod_code + '">' + item.shipfeeFormatStr + '</label></td></tr>';
    });
    html += '</tbody></table>';
    jQuery('.modal-shipping .shipping-method').html(html);
}

function b2wl_load_shipping_info(product_id, country_from, country_to, page = 'search', callback = null) {
    var data = { 'action': 'b2wl_load_shipping_info', 'id': product_id, 'country_from': country_from, 'country_to': country_to, 'page': page };

    jQuery.post(ajaxurl, data).done(function (response) {
        var json = jQuery.parseJSON(response);
        if (json.state !== 'ok') {
            console.log(json);
            if (callback) { callback(json.state, [], '', '', []) }
        }
        if (json.state !== 'error' && callback) {
            const product = json.products.length > 0 ? json.products[0] : false
            callback(json.state, product ? product.items : [], product ? product.default_method : '', product ? product.shipping_cost : 0, product ? product.variations : [])

            if (product && product.items.length > 0) {
                jQuery.post(ajaxurl, { 'action': 'b2wl_update_shipping_list', items: product.items })
            }

        }

    }).fail(function (xhr, status, error) {
        console.log(error);
    });
}

function b2wl_calc_profit(product_id) {
    var product = jQuery('.product[data-id="' + product_id + '"]');

    jQuery(product).find('.price').each(function (index) {
        var _row = jQuery(this).parents('tr');

        var external_price = parseFloat(jQuery(_row).find('.external-price').attr('data-value')) || 0;
        var external_shipping = parseFloat(jQuery(_row).find('.external-shipping').attr('data-value')) || 0;
        var price = parseFloat(jQuery(_row).find('.price').val()) || 0;
        var profit = Math.round((price - external_price - external_shipping) * 100) / 100;
        jQuery(_row).find('.profit').removeClass('positive').removeClass('negative');
        jQuery(_row).find('.profit').addClass(profit > 0 ? 'positive' : 'negative');
        jQuery(_row).find('.profit .value').html(profit);
    });
}

function b2wl_update_product_prices(product_id, shipping_cost, variations) {
    const product = jQuery('.product[data-id="' + product_id + '"]');
    jQuery.each(variations, function (_, v) {
        jQuery(product).find('.variants-table [data-id="' + v.id + '"] .price').val(v.calc_price)
        jQuery(product).find('.variants-table [data-id="' + v.id + '"] .regular_price').val(v.calc_regular_price)
    });

    jQuery(product).find('.external-shipping').html(jQuery(product).find('.external-shipping').attr('data-currency') + shipping_cost);
    jQuery(product).find('.external-shipping').attr('data-value', shipping_cost);
    b2wl_calc_profit(product_id);
}

function b2wl_get_product_proc(params, callback) {
    if (typeof b2wl_get_product === "function") {
        b2wl_get_product(params, callback);
    } else {
        callback('error', false, 'Please install and activate the bng2woo-lite chrome extension in your browser: <a href="' + jQuery('#b2wl_chrome_url').val() + '">Get Chrome Extension</a>');
    }
}

function b2wl_js_update_product(products_to_update, state, on_load_calback) {
    if (products_to_update.length > 0) {
        var data = products_to_update.shift();

        const post_import = function (post_data = {}) {
            jQuery.post(ajaxurl, post_data).done(function (response) {
                var json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                }

                if (json.state === 'error') {
                    state.update_error_cnt += data.ids.length;
                } else {
                    state.update_error_cnt += json.update_state.error;
                    state.update_cnt += json.update_state.ok;
                }

                if (on_load_calback) {
                    on_load_calback(json.state, state);
                }

                b2wl_js_update_product(products_to_update, state, on_load_calback);
            }).fail(function (xhr, status, error) {
                console.log(error);
                state.update_error_cnt += data.ids.length;

                if (on_load_calback) {
                    on_load_calback('error', state);
                }

                b2wl_js_update_product(products_to_update, state, on_load_calback);
            });
        }


        if (b2wl_wc_pl_script.chrome_ext_import && data.action == 'b2wl_sync_products') {
            var external_id = jQuery('#b2wl-' + data.ids[0]).attr('data-external-id');
            b2wl_get_product_proc({ id: external_id, locale: b2wl_wc_pl_script.locale, curr: b2wl_wc_pl_script.currency }, function (apd_state, apd, msg) {
                if (apd_state !== 'error') {
                    const apd_items = [{ id: external_id, apd }]
                    post_import({ ...data, apd_items: apd_items })
                } else {
                    console.log('Error! b2wl_get_product: ', msg);
                    state.update_error_cnt += data.ids.length;
                    if (on_load_calback) {
                        on_load_calback('error', state);
                    }
                    b2wl_js_update_product(products_to_update, state, on_load_calback);
                }
            });
        } else {
            post_import(data)
        }
    }
}

function Utils() { }
Utils.prototype = {
    constructor: Utils,
    isElementInView: function (element, fullyInView) {
        var pageTop = jQuery(window).scrollTop();
        var pageBottom = pageTop + jQuery(window).height();
        var elementTop = jQuery(element).offset().top;
        var elementBottom = elementTop + jQuery(element).height();

        if (fullyInView === true) {
            return ((pageTop < elementTop) && (pageBottom > elementBottom));
        } else {
            return ((elementTop <= pageBottom) && (elementBottom >= pageTop));
        }
    }
};
var Utils = new Utils();

(function ($, window, document, undefined) {
    $(function () {
        if (b2wl_chrome_extension_loaded === false) {
            $("#chrome-notify").show();
        }

        /* ##################### Sync Product with Banggood (Product Detail Page) ############### */

        var b2wl_update_action_lock = false;

        $(".sync-bng-product").click(function (e) {

            e.preventDefault();

            if (!b2wl_update_action_lock) {
                b2wl_update_action_lock = true;
                var products_to_update = [];
                var data = { 'action': 'b2wl_sync_products', 'ids': [] }
                data.ids.push($(this).data('id'));

                if (data.ids.length > 0) {
                    products_to_update.push(data);

                    var on_load = function (response_state, state) {
                        if ((state.update_cnt + state.update_error_cnt) === state.num_to_update) {
                            b2wl_update_action_lock = false;
                            alert(b2wl_sync_data.lang.sync_successfully);
                            location.reload();
                        } else {
                            alert(b2wl_sync_data.lang.sync_failed);
                        }
                    };

                    var state = { num_to_update: 1, update_cnt: 0, update_error_cnt: 0 };
                    b2wl_js_update_product(products_to_update, state, on_load);
                }
            }
        });
        /* ######################################################################################## */

        $(".country_list").select2();

        $("#b2w_local_currency").select2();
        $("#b2w_import_language").select2();

        $("img.lazy").lazyload && $("img.lazy").lazyload({ effect: "fadeIn" });

        $("#b2wl-do-filter").click(function () {
            $(this).closest('form').submit();
            return false;
        });

        jQuery("#b2wl-search-form").submit(function () {
            jQuery(this).find(":input").filter(function () {
                return !this.value;
            }).attr("disabled", "disabled");
            return true;
        });

        $("#b2wl-sort-selector").change(function () {
            jQuery("#b2wl-search-form #b2wl_sort").val($(this).val());
            jQuery("#b2wl-search-form").submit();
            return false;
        });

        $("#b2wl-search-pagination li a").click(function () {
            jQuery("#b2wl-search-form #cur_page").val($(this).attr('rel'));
            jQuery("#b2wl-search-form").submit();

            return false;
        });

        /* ====================== Override ====================== */

        $(".product").on("click", ".product-card-override-product", function () {
            $(this).parents('div.btn-group').removeClass('open');
            $(".modal-override-product .do-override-product").removeClass('load');
            $('.modal-override-product .modal-body').addClass('load');
            $(".modal-override-product").addClass('opened');
            $(".modal-override-product .override-error").html('');
            $('#b2wl-override-select-products').val('').trigger("change");

            let product = $(this).parents('.product').data();
            $(".modal-override-product").attr('id', product.id);
            jQuery.post(ajaxurl, { 'action': 'b2wl_get_product', 'id': product.id }).done(function (response) {
                let json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                } else {
                    $(".modal-override-product").data(json.product);
                    $('.modal-override-product .override-with').html('<div class="b2wl-item-override"><img src="' + json.product.thumb + '"/><div class="product-title">' + json.product.title + '</div></div>');
                }
                $('.modal-override-product .modal-body').removeClass('load');
            }).fail(function (xhr, status, error) {
                console.log(error);
            });

            return false;
        });

        $('#b2wl-override-select-products').select2({
            minimumInputLength: 3,
            allowClear: true,
            templateResult: function (product) {
                if (product.id) {
                    return $('<div class="b2wl-item-override dd"><img src="' + product.thumb + '"/><div class="product-title">' + product.text + '</div></div>');
                } else {
                    return product.text;
                }
            },
            templateSelection: function (product) {
                if (product.id) {
                    return $('<div class="b2wl-item-override dd"><img src="' + product.thumb + '"/><div class="product-title">' + product.text + '</div></div>');
                } else {
                    return product.text;
                }
            },
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                data: function (params) {
                    var query = {
                        action: 'b2wl_search_products',
                        search: params.term,
                        page: params.page || 1
                    }
                    return query;
                }
            }
        });

        $('#b2wl-override-select-products').on('change', function () {
            const productId = $('#b2wl-override-select-products').val();
            if (productId) {
                $('.modal-override-product .override-options').show();

                const data = { action: 'b2wl_override_order_variations', product_id: productId };

                jQuery.post(ajaxurl, data).done(function (response) {
                    let json = jQuery.parseJSON(response);
                    if (json.state !== 'ok') {
                        console.log(json);
                        $(".modal-override-product .override-error").html('<div class="b2wl-danger">' + json.message + '</div>');
                        $('.modal-override-product .do-override-product').attr('disabled', 'disabled');
                    } else {
                        if (json.order_variations.length > 0) {
                            let total_orders = 0;
                            $.each(json.order_variations, function (i, variation) {
                                total_orders += variation.cnt;
                            });

                            // render variation override
                            $('.modal-override-product .override-order-variations').html('<div class="b2wl-info" style="margin-top: 12px;">You have <b>' + total_orders + ' unfulfilled orders</b> of the product are you trying to override</div>');
                            $.each(json.order_variations, function (i, variation) {
                                if (i == 0) {

                                    $('.modal-override-product .override-order-variations').append('<div class="override-items" style="margin-top: 0px;" data-variation-id="' + variation.variation_id + '"><div class="override-item"><div class="item-title" style="font-weight: bold;">Old variations</div><div class="item-body"><div class="b2wl-item-override"><img src="' + variation.thumbnail + '"/><div class="product-title">' + variation.variation_attributes + '<br/><span>In ' + variation.cnt + ' orders</span></div></div></div></div><div class="b2wl-icon-arrow-right variation-delimiter"></div><div class="override-item"><div class="item-title" style="font-weight: bold;">New variations</div><div class="item-body"><select style="width:100%" class="form-control override-order-variation" data-placeholder="Search variations"></select></div></div></div>');
                                } else {
                                    $('.modal-override-product .override-order-variations').append('<div class="override-items" style="margin-top: 0px;" data-variation-id="' + variation.variation_id + '"><div class="override-item"><div class="item-body"><div class="b2wl-item-override"><img src="' + variation.thumbnail + '"/><div class="product-title">' + variation.variation_attributes + '<br/><span>In ' + variation.cnt + ' orders</span></div></div></div></div><div class="b2wl-icon-arrow-right variation-delimiter"></div><div class="override-item"><div class="item-body"><select style="width:100%" class="form-control override-order-variation" data-placeholder="Search variations"></select></div></div></div>');
                                }

                            });

                            const product = $(".modal-override-product").data();
                            const variations_data = product.sku_products.variations.map(function (v) { return { id: v.id, text: v.attributes_names.join('#'), thumb: v.image } });
                            $(".override-order-variations .override-order-variation").select2({
                                allowClear: true,
                                data: variations_data,
                                templateResult: function (product) {
                                    if (product.id) {
                                        return $('<div class="b2wl-item-override dd"><img src="' + product.thumb + '"/><div class="product-title">' + product.text + '</div></div>');
                                    } else {
                                        return product.text;
                                    }
                                },
                                templateSelection: function (product) {
                                    if (product.id) {
                                        return $('<div class="b2wl-item-override dd"><img src="' + product.thumb + '"/><div class="product-title">' + product.text + '</div></div>');
                                    } else {
                                        return product.text;
                                    }
                                }
                            }).val(null).trigger('change');

                            $('.modal-override-product .override-order-variations').show();
                        } else {
                            $('.modal-override-product .override-order-variations').hide();
                            $('.modal-override-product .do-override-product').removeAttr('disabled');
                        }
                    }

                }).fail(function (xhr, status, error) {
                    console.log(error);
                    $(".modal-override-product .override-error").html('<div class="b2wl-danger">Request error.</div>');
                    $('.modal-override-product .do-override-product').attr('disabled', 'disabled');
                });
            } else {
                $('.modal-override-product .override-options').hide();
                $('.modal-override-product .override-order-variations').hide();
                $('.modal-override-product .do-override-product').attr('disabled', 'disabled');
            }
        });


        $('.override-order-variations').on('change', '.override-order-variation', function () {
            let can_override = true;
            $(".override-order-variations .override-order-variation").each(function () {
                if (!$(this).val()) {
                    can_override = false
                }
            });

            if (can_override) {
                $('.modal-override-product .do-override-product').removeAttr('disabled');
            } else {
                $('.modal-override-product .do-override-product').attr('disabled', 'disabled');
            }
        });

        $(".modal-override-product").on("click", ".do-override-product", function () {
            var thisBtn = $(this)
            thisBtn.addClass('load')
            const external_id = $(this).parents('.modal-override-product').attr('id');
            let productDiv = $('.product[data-id="' + external_id + '"]');

            const order_variations = []
            $(".override-order-variations .override-order-variation").each(function () {
                if ($(this).val()) {
                    order_variations.push({ variation_id: $(this).parents('.override-items').attr('data-variation-id'), external_variation_id: $(this).val() });
                }
            });

            const data = {
                action: 'b2wl_override_product',
                product_id: $('#b2wl-override-select-products').val(),
                external_id: external_id,
                override_images: $('#b2wl-override-images').is(':checked'),
                override_title_description: $('#b2wl-override-title-description').is(':checked'),
                order_variations: order_variations
            };

            jQuery.post(ajaxurl, data).done(function (response) {
                let json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                    $(".modal-override-product .override-error").html('<div class="b2wl-danger">' + json.message + '</div>');
                } else {
                    productDiv.find('.product-info-container').html('<div class="b2wl-warning">' + json.html + '</div>');
                    productDiv.find('.post_import .btn-title').text(json.button);
                    productDiv.find('.product-card-override-product').parents('li').remove();
                    thisBtn.parents('.modal-overlay').removeClass('opened');
                }
                thisBtn.removeClass('load');
            }).fail(function (xhr, status, error) {
                console.log(error);
                $(".modal-override-product .override-error").html('<div class="b2wl-danger">Request error.</div>');
                thisBtn.removeClass('load');
            });

            return false;
        });

        $(".product").on("click", ".cancel-override", function () {
            let thisBtn = $(this);
            let productDiv = $(this).parents('.product');
            let product = $(this).parents('.product').data();
            $(".modal-override-product").attr('id', product.id);
            thisBtn.attr('disabled', 'disabled');
            jQuery.post(ajaxurl, { 'action': 'b2wl_cancel_override_product', 'external_id': product.id }).done(function (response) {
                let json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                    show_notification('Save failed: ' + json.message, true);
                    thisBtn.removeAttr('disabled');
                } else {
                    show_notification('Saved successfully.');
                    productDiv.find('.product-info-container').html('');
                    productDiv.find('.post_import .btn-title').text(json.button);
                    productDiv.find('.actions ul.dropdown-menu').prepend(json.override_action);
                }

            }).fail(function (xhr, status, error) {
                console.log(error);
                show_notification('Save failed: request error.', true);
                thisBtn.removeAttr('disabled');
            });

            return false;
        });



        /* ====================================================== */

        $(".product-card-split-product").on("click", function () {
            $(this).parents('div.btn-group').removeClass('open');
            $('.modal-split-product .modal-body').addClass('load');
            $(".modal-split-product").addClass('opened');

            let product = $(this).parents('.product').data();
            $(".modal-split-product").attr('id', product.id);
            jQuery.post(ajaxurl, { 'action': 'b2wl_get_product', 'id': product.id }).done(function (response) {
                let json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                } else {
                    $('.modal-split-product').data('product', json.product);
                    show_split_content(json.product, 'attributes');
                }
                $('.modal-split-product .btn-split-count').text($('.modal-split-product .split-attributes input:checked').attr('count'));
                $('.modal-split-product .modal-body').removeClass('load');

            }).fail(function (xhr, status, error) {
                console.log(error);
            });

            return false;
        });

        function show_split_content(product, mode) {
            $('.modal-split-product .split-content').attr('data-mode', mode)
            $('.modal-split-product .split-attributes').html('');
            if (mode == 'attributes') {
                $('.modal-split-product .split-name').text('Select which option you want to use for splitting the product');
                $('.modal-split-product .split-mode').text('Split manually');

                $('.modal-split-product .do-split-product.attributes').show()
                $('.modal-split-product .do-split-product.manual').hide()

                $('.modal-split-product .do-split-product').removeAttr('disabled');

                $.each(product.sku_products.attributes, function (i, attr) {
                    const attrKeys = Object.keys(attr.value).filter(k => !attr.value[k].original_id)
                    if (attrKeys.length > 1) {
                        let values = attrKeys.map(function (k) { return attr.value[k] && attr.value[k].name }) || [];
                        $('.modal-split-product .split-attributes').append('<div class="split-attr"><div class="row" style="display:flex;align-items: center;"><div class="col-xs-2" style="display:flex;align-items: center;"><input type="radio" id="' + attr.id + '" name="split_attr" value="' + attr.id + '" count="' + values.length + '" ' + (i === 0 ? 'checked="checked"' : '') + '/><label for="' + attr.id + '">' + attr.name + '</label></div><div class="col-xs-10">We will create ' + values.length + ' products each containing only distinct "' + attr.name + '" variants (' + values.join(', ') + ')</div></div></div>');
                    }
                });
            } else if (mode == 'manual') {
                $('.modal-split-product .split-name').text('Select variants you wish to split to another product');
                $('.modal-split-product .split-mode').text('Split by option');

                $('.modal-split-product .do-split-product.attributes').hide()
                $('.modal-split-product .do-split-product.manual').show()

                $('.modal-split-product .do-split-product').attr('disabled', 'disabled');

                $.each(product.sku_products.attributes, function (i, attr) {
                    const attrKeys = Object.keys(attr.value).filter(k => !attr.value[k].original_id)
                    if (attrKeys.length > 1) {
                        let values = attrKeys.map(function (k) { return attr.value[k] && { id: attr.value[k].id, name: attr.value[k].name } }) || [];
                        const valuesHtml = values.map(val => '<div><input type="checkbox" id="' + val.id + '" class="split_attr_value" value="' + val.id + '"/><label for="' + val.id + '">' + val.name + '</label></div>').join('');
                        $('.modal-split-product .split-attributes').append('<div class="split-attr"><b>' + attr.name + '</b><div class="attr-values">' + valuesHtml + '</div></div>');
                    }
                });

                const attributes = product.sku_products.attributes.map(a => '<th>' + a.name + '</th>').join('')
                $('.modal-split-product .split-attributes').append('<table class="split-vars"><tr><th colspan="2"><div><input type="checkbox" id="split-check-all"/><label for="split-check-all">Use all</label></div></th><th>SKU</th>' + attributes + '<th>Cost</th><th>Price</th><th>Regular Price</th><th>Inventory</th></tr></table>');
                $.each(product.sku_products.variations, function (i, v) {
                    const attributes_names = v.attributes_names.map(a => '<td>' + a + '</td>').join('');
                    $('.modal-split-product .split-vars').append('<tr><td><input type="checkbox" class="split_selected_vars" value="' + v.id + '"/></td><td>' + (v.image ? '<img src="' + v.image + '" width="40"/>' : '') + '</td><td>' + v.sku + '</td>' + attributes_names + '<td>' + v.price + '</td><td>' + v.calc_price + '</td><td>' + v.calc_regular_price + '</td><td>' + v.quantity + '</td></tr>')
                });
            }
        }

        $(".modal-split-product .split-mode").on("click", function () {
            const mode = $(".modal-split-product .split-content").attr('data-mode') == 'attributes' ? 'manual' : 'attributes';
            $(".modal-split-product .split-content").attr('data-mode', mode);
            show_split_content($('.modal-split-product').data('product'), mode);
        });

        $(".modal-split-product").on("change", ".split_attr_value", function () {
            const this_ = this
            const product = $('.modal-split-product').data('product');
            const variations = product.sku_products.variations.filter(v => v.attributes.indexOf($(this_).val()) != -1)
            $.each(variations, function (i, v) {
                $('.modal-split-product .split_selected_vars[value="' + v.id + '"]').prop('checked', $(this_).is(':checked')).change();;
            });
        });

        $(".modal-split-product").on("change", "#split-check-all", function () {
            $('.modal-split-product .split_attr_value').prop('checked', false);
            var checkboxes = $('.modal-split-product .split-vars :checkbox').not($(this));
            if ($(this).is(':checked')) {
                checkboxes.prop('checked', true).change();
            } else {
                checkboxes.prop('checked', false).change();
            }
        });

        $(".modal-split-product").on("change", ".split_selected_vars", function () {
            if ($('.modal-split-product .split_selected_vars:checked').length > 0) {
                $('.modal-split-product .do-split-product').removeAttr('disabled');
            } else {
                $('.modal-split-product .do-split-product').attr('disabled', 'disabled');
            }
        });

        $(".modal-split-product").on("change", ".split-attr input", function () {
            $('.modal-split-product .btn-split-count').text($('.modal-split-product .split-attributes input:checked').attr('count'));
        });

        $(".modal-split-product .do-split-product").on("click", function () {
            let data = { 'action': 'b2wl_split_product', 'id': $(".modal-split-product").attr('id') };
            if ($('.modal-split-product .split-content').attr('data-mode') == 'attributes') {
                data.attr = $('.modal-split-product .split-attributes input:checked').val();
            } else {
                data.vars = []
                $('.modal-split-product .split_selected_vars:checked').each(function () { data.vars.push($(this).val()) });
            }

            jQuery.post(ajaxurl, data).done(function (response) {
                let json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                }

                location.reload();
            }).fail(function (xhr, status, error) {
                console.log(error);
            });

            return false;
        });
        /* ================================================================================ */

        $(".product-card-shipping-info").on("click", function () {
            const tmp_data = $(this).data();

            const onSelectCallback = function (product_id, items, country, method) {
                const product_block = jQuery('.product-card[data-id="' + product_id + '"]');

                const item = items.find(function (s) { return s.shipmethod_code == method })

                if (item) {
                    const price = item.shipfee;
                    const formated_price = price > 0.009 ? item.shipfeeFormatStr : 'Free';

                    jQuery(product_block).find('.product-card-shipping-info .shipping-title').html(formated_price + ' ' + item.shipmethod_name);
                    jQuery(product_block).find('.product-card-shipping-info .delivery-time').html(item.shipday + ' days');
                    jQuery(product_block).find('.product-card-shipping-info').data({ 'country': country, 'shipping': items, 'default_method': method });
                }
            }

            fill_modal_shipping_info($(this).closest(".product-card").attr('data-id'), tmp_data.country ? tmp_data.country : "", tmp_data.shipping ? tmp_data.shipping : [], 'search', tmp_data.default_method ? tmp_data.default_method : '', onSelectCallback);

            $(".modal-shipping").addClass('opened');
            return false;
        });

        $(".product .shipping-country").on("click", function () {
            const product = $(this).parents(".product");
            const product_data = $(product).data();
            const onSelectCallback = function (product_id, items, country_from, country_to, method) {
                const data = { 'action': 'b2wl_set_shipping_info', 'id': product_id, country_from, country_to, method };
                jQuery.post(ajaxurl, data).done(function (response) {
                    const json = jQuery.parseJSON(response);
                    if (json.state !== 'ok') {
                        console.log(json);
                    } else {
                        const shiping_to_name = $("#modal-country-select option:selected").text()
                        if (shiping_to_name) {
                            $(product).find('.shipping-country').html(shiping_to_name)
                        }
                        b2wl_update_product_prices(product_id, json.shipping_cost, json.variations);
                        jQuery(product).data({ ...product_data, shipping: items, country_from, country_to, default_method: json.default_method });
                    }
                }).fail(function (xhr, status, error) {
                    console.log(error);
                });
            }


            const product_id = $(this).parents(".product").attr('data-id')

            const country_from_list = product_data.country_from_list.split(';').filter(c => c)
            if (!product_data.country_to) {
                fill_modal_shipping_info(product_id, country_from_list, product_data.country_from || "", "", null, 'import', '', onSelectCallback);
            } else if (!product_data.shipping) {
                b2wl_load_shipping_info(product_id, product_data.country_from || '', product_data.country_to || '', 'import', function (state, items, default_method, shipping_cost, variations) {
                    if (state != 'error') {
                        fill_modal_shipping_info(product_id, country_from_list, product_data.country_from || "", product_data.country_to || "", items, 'import', product_data.default_method || default_method, onSelectCallback);
                    }
                })
            } else {
                fill_modal_shipping_info(product_id, country_from_list, product_data.country_from || "", product_data.country_to || "", product_data.shipping, 'import', product_data.default_method, onSelectCallback);
            }
            $(".modal-shipping").addClass('opened');
            return false;
        });

        $('#b2wl-modal-country-from-select').on('change', function () {
            const shipping_data = $(".modal-shipping").data();
            if ($(this).val() && $(this).val() != "" && shipping_data.country_from != $(this).val()) {
                shipping_data.country_from = $(this).val();
                $(".modal-shipping").data(shipping_data);

                $('.modal-shipping .shipping-method').html('<div class="b2wl-load-container"><div class="b2wl-load-speeding-wheel"></div></div>');

                b2wl_load_shipping_info(shipping_data.product_id, shipping_data.country_from, shipping_data.country_to, shipping_data.page, function (state, items, default_method, shipping_cost, variations) {
                    fill_modal_shipping_info(shipping_data.product_id, shipping_data.country_from_list, shipping_data.country_from, shipping_data.country_to, items, shipping_data.page, default_method, shipping_data.onSelectCallback);
                    if (state != 'error') {
                        if (shipping_data.onSelectCallback) {
                            shipping_data.onSelectCallback(shipping_data.product_id, items, shipping_data.country_from, shipping_data.country_to, default_method)
                        }
                    }
                })
            }
        });

        $('#b2wl-modal-country-select').on('change', function () {
            const shipping_data = $(".modal-shipping").data();
            if ($(this).val() && $(this).val() != "" && shipping_data.country_to != $(this).val()) {
                shipping_data.country_to = $(this).val();
                $(".modal-shipping").data(shipping_data);

                $('.modal-shipping .shipping-method').html('<div class="b2wl-load-container"><div class="b2wl-load-speeding-wheel"></div></div>');

                b2wl_load_shipping_info(shipping_data.product_id, shipping_data.country_from, shipping_data.country_to, shipping_data.page, function (state, items, default_method, shipping_cost, variations) {
                    fill_modal_shipping_info(shipping_data.product_id, shipping_data.country_from_list, shipping_data.country_from, shipping_data.country_to, items, shipping_data.page, default_method, shipping_data.onSelectCallback);
                    if (state != 'error') {
                        if (shipping_data.onSelectCallback) {
                            shipping_data.onSelectCallback(shipping_data.product_id, items, shipping_data.country_from, shipping_data.country_to, default_method)
                        }
                    }
                })
            }
        });

        $('.modal-shipping').on('change', '.select_method', function () {
            const shipping_data = $(".modal-shipping").data();
            const delay = shipping_data.page == "search" ? 0 : 1000
            const _this_value = this.value;

            waitForFinalEvent(function () {
                if (shipping_data.onSelectCallback) {
                    shipping_data.onSelectCallback(shipping_data.product_id, shipping_data.shipping, shipping_data.country_from, shipping_data.country_to, _this_value)
                }
            }, delay, "change_shipping_method");
        });

        function b2wl_js_add_to_import(products_to_import, state, befor_load_calback, on_load_calback) {
            if (products_to_import.length > 0) {
                var data = products_to_import.shift();

                if (befor_load_calback) {
                    befor_load_calback(data.id, state);
                }

                const post_import = function (post_data = {}) {
                    jQuery.post(ajaxurl, post_data).done(function (response) {
                        var json = jQuery.parseJSON(response);
                        if (json.state !== 'ok') {
                            console.log(json);
                        }
                        if (json.state === 'error') {
                            state.import_error_cnt++;

                        } else {
                            state.import_cnt++;
                        }

                        if (on_load_calback) {
                            on_load_calback(data.id, json.state, json.message, state);
                        }

                        b2wl_js_add_to_import(products_to_import, state, befor_load_calback, on_load_calback);
                    }).fail(function (xhr, status, error) {
                        console.log(error);
                        state.import_error_cnt++;

                        if (on_load_calback) {
                            on_load_calback(data.id, 'error', 'request error', state);
                        }

                        b2wl_js_add_to_import(products_to_import, state, befor_load_calback, on_load_calback);
                    });
                }

                post_import(data);
            }
        }

        $('.b2wl-content .import_all').click(function () {
            var this_btn = $(this);
            $('.modal-confirm').confirm_modal({
                title: 'Add all to import list',
                body: 'Are you sure you want add all product to import list?',
                yes: function () {
                    products_to_import = [];
                    $('.product-card:not(.product-card--added)').each(function () {
                        products_to_import.push({ 'action': 'b2wl_add_to_import', 'id': $(this).attr('data-id') });
                    });
                    if (products_to_import.length > 0) {
                        $(this_btn).addClass('load');

                        var on_befor_import = function (id, state) {
                            $('.product-card[data-id="' + id + '"] .product-card__actions .btn').addClass('load');
                        };

                        var on_import_load = function (id, response_state, response_message, state) {
                            if ((state.import_cnt + state.import_error_cnt) === state.num_to_import) {
                                $(this_btn).removeClass('load');
                            }

                            if (response_state !== 'ok') {
                                show_notification('Import failed. ' + response_message, true);
                            } else {
                                show_notification('Imported successfully.');
                                if ($('.product-card[data-id="' + id + '"]').length > 0) {
                                    $('.product-card[data-id="' + id + '"]').addClass("product-card--added");
                                    $('.product-card[data-id="' + id + '"] .product-card__actions .btn .title').text('Remove from import list');
                                    $('.product-card[data-id="' + id + '"] .product-card__actions .btn').removeClass('btn-success');
                                    $('.product-card[data-id="' + id + '"] .product-card__actions .btn').addClass('btn-default');
                                }
                            }

                            if ($('.product-card[data-id="' + id + '"]').length > 0) {
                                $('.product-card[data-id="' + id + '"] .product-card__actions .btn').removeClass('load');
                            }
                        };

                        var state = { num_to_import: products_to_import.length, import_cnt: 0, import_error_cnt: 0 };
                        b2wl_js_add_to_import(products_to_import, state, on_befor_import, on_import_load);
                        b2wl_js_add_to_import(products_to_import, state, on_befor_import, on_import_load);
                    }
                }
            });
            return false;
        });

        $(".product-card").find(".product-card__actions .btn").on("click", function () {
            var _this = $(this);
            $(_this).addClass("load");
            if ($(_this).closest(".product-card").hasClass('product-card--added')) {
                $(_this).addClass("btn-success");
                $(_this).removeClass("btn-default");

                var data = { 'action': 'b2wl_remove_from_import', 'id': $(_this).closest(".product-card").attr('data-id') };
                jQuery.post(ajaxurl, data).done(function (response) {
                    var json = jQuery.parseJSON(response);
                    if (json.state !== 'ok') {
                        console.log(json);
                        show_notification('Import failed. ' + json.message, true);
                    } else {
                        show_notification('Imported successfully.');

                        $(_this).closest(".product-card").removeClass("product-card--added");
                        $(_this).find(".title").text('Add to import list');
                    }

                    $(_this).removeClass("load");
                }).fail(function (xhr, status, error) {
                    console.log(error);
                    show_notification('Import failed. ' + error, true);
                    $(_this).removeClass("load");

                });
            } else {
                products_to_import = [];
                products_to_import.push({ 'action': 'b2wl_add_to_import', 'id': $(_this).closest(".product-card").attr('data-id') });

                var on_import_load = function (id, response_state, response_message, state) {
                    if (response_state !== 'ok') {
                        show_notification('Import failed. ' + response_message, true);
                    } else {
                        show_notification('Imported successfully.');
                        if ($('.product-card[data-id="' + id + '"]').length > 0) {
                            $('.product-card[data-id="' + id + '"]').addClass("product-card--added");
                            $('.product-card[data-id="' + id + '"] .product-card__actions .btn .title').text('Remove from import list');
                        }
                    }

                    if ($('.product-card[data-id="' + id + '"]').length > 0) {
                        $('.product-card[data-id="' + id + '"] .product-card__actions .btn').removeClass('load');
                    }
                };
                var state = { num_to_import: products_to_import.length, import_cnt: 0, import_error_cnt: 0 };
                b2wl_js_add_to_import(products_to_import, state, null, on_import_load);

            }
        });

        $("#import-by-id-url-btn").on("click", function () {
            var _this_btn = $(this);
            var url_value = $(this).parents('.modal-content').find('#url_value').val();
            var id_value = $.trim($(this).parents('.modal-content').find('#id_value').val());

            var product_id = '';
            if (id_value) {
                product_id = id_value;
            } else if (url_value) {
                var result = url_value.match(/.*p-([0-9]+)\.html/i);
                if (result && result.length > 1) {
                    product_id = result[1];
                }
            }
            if (product_id) {
                $(_this_btn).attr("disabled", true);
                var product_block = $('.product-card[data-id="' + product_id + '"]');

                products_to_import = [];
                products_to_import.push({ 'action': 'b2wl_add_to_import', 'id': product_id });

                var on_import_load = function (id, response_state, response_message, state) {
                    if (response_state !== 'ok') {

                        show_notification('Import failed. ' + response_message, true);
                    } else {
                        show_notification('Imported successfully.');
                        if ($('.product-card[data-id="' + id + '"]').length > 0) {
                            $('.product-card[data-id="' + id + '"]').addClass("product-card--added");
                            $('.product-card[data-id="' + id + '"] .product-card__actions .btn .title').text('Remove from import list');
                        }
                    }

                    $(product_block).removeClass("loading");
                    $(".modal-overlay").removeClass('opened');
                    $("#import-by-id-url-btn").removeAttr('disabled');
                    $(_this_btn).removeAttr("disabled");
                };
                var state = { num_to_import: products_to_import.length, import_cnt: 0, import_error_cnt: 0 };
                b2wl_js_add_to_import(products_to_import, state, null, on_import_load);
            } else { }
        });

        $(".country-select__trigger").on("click", function () {
            $(this).siblings(".country-select__list-wrap").slideToggle('400');
        });

        $(".country-select").find(".country-select__item").on("click", function () {
            var val = $(this).html();
            $(this).closest(".country-select").find(".country-select__trigger").html(val).end().find(".country-select__list-wrap").fadeOut(300);
        });

        $(".modal-search-open").on("click", function () {
            $('.modal-search input[type="text"]').val('');
            $("#import-by-id-url-btn").removeAttr('disabled');
            $(".modal-search").addClass('opened');
        });

        $(".modal-close, .modal-btn-close").on("click", function () {
            $(".modal-overlay").removeClass('opened');
            return false;
        });

        $(document).mouseup(function (e) {
            var div = $(".country-select__list-wrap, .dropdown-menu");
            if (!div.is(e.target) && div.has(e.target).length === 0) {
                div.hide();
            }
        });

        $(window).resize(resizeRowproducts);
        resizeRowproducts();
        function resizeRowproducts() {
            $(".search-result__row").height($(".product-card").innerHeight());
        }

        $(".product-card__shipping-info").on("click", function () {
            $(".modal-overlay-shipping").addClass('opened');
        });


        $.fn.confirm_modal = function (options) {
            var confirm_modal_dialog = this;

            $(confirm_modal_dialog).find('.modal-header .modal-title').html(options.title ? options.title : '');
            $(confirm_modal_dialog).find('.modal-body').html(options.body ? options.body : '');

            $(confirm_modal_dialog).find(".btn.no-btn").on("click", function () {
                if (options && options.no) {
                    options.no();
                }
                $(confirm_modal_dialog).removeClass('opened');
            });
            $(confirm_modal_dialog).find(".btn.yes-btn").on("click", function () {
                if (options && options.yes) {
                    options.yes();
                }
                $(confirm_modal_dialog).removeClass('opened');
            });
            $(document).ready(function () {
                $(".modal-confirm").addClass('opened');
            });
            return this;
        };

        function update_bulk_actions() {
            if ($('.b2wl-product-import-list .select :checkbox:checked').length > 0) {
                $("#b2wl-import-actions .action-with-check select option").first().text('Bulk Actions (' + $('.b2wl-product-import-list .select :checkbox:checked').length + ' selected)');
                $('.action-with-check').show();
            } else {
                $('.action-with-check').hide();
            }
        }

        $('#b2wl-import-actions .check-all').change(function () {
            var checkboxes = $('.b2wl-product-import-list .select :checkbox').not($(this));
            if ($(this).is(':checked')) {
                checkboxes.prop('checked', true);
            } else {
                checkboxes.prop('checked', false);
            }
            update_bulk_actions();
        });

        $('.variants-table .check-all-var').change(function () {
            var checkboxes = $(this).closest('.variants-table').find(':checkbox').not($(this));
            if ($(this).is(':checked')) {
                checkboxes.prop('checked', true);
            } else {
                checkboxes.prop('checked', false);
            }

            var $product = $(this).closest('.product');

            var skip_vars = [];
            $($product).find('.variants-table .check-var').not(':checked').each(function () {
                skip_vars.push($(this).closest('tr').attr('data-id'));
            });

            if (!skip_vars.length) {
                b2wl_update_product($product.attr('data-id'), { reset_skip_vars: true });
            } else {
                b2wl_update_product($product.attr('data-id'), { skip_vars: skip_vars });
            }

        });

        $('.variants-table .check-var').change(function () {
            var $product = $(this).closest('.product');

            var skip_vars = [];
            $($product).find('.variants-table .check-var').not(':checked').each(function () {
                skip_vars.push($(this).closest('tr').attr('data-id'));
            });

            if (!skip_vars.length) {
                b2wl_update_product($product.attr('data-id'), { reset_skip_vars: true });
            } else {
                b2wl_update_product($product.attr('data-id'), { skip_vars: skip_vars });
            }
        });

        function recalc_image_block_selector() {
            $("[rel=images] .images-blog-title").each(function () {
                $(this).find('.check-all-block-image').prop('checked', jQuery(this).next().find('.image.selected').length === jQuery(this).next().find('.image').length);
            });;
        }

        recalc_image_block_selector();



        $('[rel=images] .images-blog-title .check-all-block-image').change(function () {
            var _block = $(this).parents('.images-blog-title');
            if ($(this).is(':checked')) {
                jQuery(_block).next().find('.image').addClass('selected');
            } else {
                jQuery(_block).next().find('.image').removeClass('selected');
                jQuery(_block).next().find('.icon-gallery-box').removeClass('selected');
            }

            var $product = $(this).closest('.product');

            var thumb_id = ($($product).find('[rel="images"] .image .icon-gallery-box.selected').length > 0) ? $($product).find('[rel="images"] .image .icon-gallery-box.selected').parents('.image').attr('id') : '';
            var skip_images_data = [];
            $($product).find('[rel="images"] .image').not('.selected').each(function () {
                skip_images_data.push($(this).attr('id'));
            });
            var data = { skip_images: skip_images_data, thumb: thumb_id };
            if (skip_images_data.length === 0) {
                data.no_skip = 1;
            }

            b2wl_update_product($product.attr('data-id'), data);

        });

        $('.b2wl-product-import-list [rel="images"] .image').click(function () {
            $(this).toggleClass('selected');

            if (!$(this).hasClass('selected')) {
                $(this).find('.selected').removeClass('selected');
            }

            recalc_image_block_selector();

            var $product = $(this).closest('.product');
            var thumb_id = ($($product).find('[rel="images"] .image .icon-gallery-box.selected').length > 0) ? $($product).find('[rel="images"] .image .icon-gallery-box.selected').parents('.image').attr('id') : '';
            var skip_images_data = [];
            $($product).find('[rel="images"] .image').not('.selected').each(function () {
                skip_images_data.push($(this).attr('id'));
            });
            var data = { skip_images: skip_images_data, thumb: thumb_id };
            if (skip_images_data.length === 0) {
                data.no_skip = 1;
            }

            b2wl_update_product($product.attr('data-id'), data);
        });

        $('.b2wl-product-import-list [rel="images"] .image .icon-selected-box').click(function () {
            $(this).toggleClass('selected');
            return false;
        });

        $('.b2wl-product-import-list [rel="images"] .image .icon-gallery-box').click(function () {
            $(this).toggleClass('selected');
            $('.b2wl-product-import-list [rel="images"] .image .icon-gallery-box').not(this).removeClass('selected');
            $(this).closest('.image').addClass('selected');
            recalc_image_block_selector();

            var $product = $(this).closest('.product');
            var thumb_id = ($($product).find('[rel="images"] .image .icon-gallery-box.selected').length > 0) ? $($product).find('[rel="images"] .image .icon-gallery-box.selected').parents('.image').attr('id') : '';
            var skip_images_data = [];
            $($product).find('[rel="images"] .image').not('.selected').each(function () {
                skip_images_data.push($(this).attr('id'));
            });
            var data = { skip_images: skip_images_data, thumb: thumb_id };
            if (skip_images_data.length === 0) {
                data.no_skip = 1;
            }
            b2wl_update_product($product.attr('data-id'), data);

            return false;
        });

        $('.description-images-action').change(function () {
            var action = $(this).val();
            var _this = this;
            $(_this).attr('disabled', 'disabled');


            var images_data = [];
            var selected_images_block = [];
            $(this).parents('.images-blog-title').next().find('.image .icon-selected-box.selected').each(function () {
                images_data.push($(this).parents('.image').attr('id'));
                selected_images_block.push($(this).parents('.image').parent());
            });

            var data = { 'action': 'b2wl_import_images_action', 'id': $(this).parents('.product').attr('data-id'), 'source': 'description', 'type': action, 'images': images_data };

            jQuery.post(ajaxurl, data).done(function (response) {
                $(_this).val('');
                $(_this).removeAttr('disabled');

                var json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                } else {
                    $.each(selected_images_block, function (i, image_block) {
                        $(image_block).find('.image').append('<div class="cancel-image-action"><a href="#" data-action="' + action + '#description">' + (action == 'move' ? 'Cancel move' : 'Cancel copy') + '</a>');
                        if (action == 'move') {
                            $(image_block).detach();
                        }
                        $(image_block).appendTo($('.row.gallery_images'));
                    });
                }
            }).fail(function (xhr, status, error) {
                $(_this).val('');
                $(_this).removeAttr('disabled');

                console.log(error);
            });

        });

        $('.b2wl-product-import-list [rel="images"] .image').on('click', '.cancel-image-action a', function () {
            var tmp = $(this).attr('data-action').split('#')
            var data = { 'action': 'b2wl_import_cancel_images_action', 'id': $(this).parents('.product').attr('data-id'), 'source': tmp[1], 'type': tmp[0], 'image': $(this).parents('.image').attr('id') };
            var image_block = $(this).parents('.image').parent();

            jQuery.post(ajaxurl, data).done(function (response) {
                var json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                } else {
                    $(image_block).detach();
                    if (tmp[0] == 'move') {
                        if (tmp[1] == 'description') {
                            $(image_block).find('.cancel-image-action').remove();
                            $(image_block).appendTo($('.row.description_images'));
                        } else if (tmp[1] == 'variant') {
                            $(image_block).find('.cancel-image-action').remove();
                            $(image_block).appendTo($('.row.variant_images'));
                        }
                    }
                }
            }).fail(function (xhr, status, error) {
                console.log(error);
            });
            return false;
        });

        $('.b2wl-product-import-list .select :checkbox').change(function () {
            update_bulk_actions();
        });

        jQuery(".b2wl-product-import-list").on("click", ".post_import", function () {
            var this_btn = this;
            var product = $(this).parents('.product');
            var products_to_import = [];
            var data = { 'action': 'b2wl_push_product', 'id': $(product).attr('data-id') };
            products_to_import.push(data);

            $(this_btn).addClass('load');
            var on_load = function (id, response_state, response_message, state) {
                if (response_state !== 'error') {
                    $('.b2wl-product-import-list .select :checkbox[value="' + id + '"]').parents('.product').parents('.row').remove();
                    if ($('.b2wl-product-import-list').find('.product').length === 0) {
                        $('#b2wl-import-content').hide();
                        $('#b2wl-import-empty').show();
                    }
                    show_notification('Push to shop complete successfully.');
                } else {
                    show_notification('Import failed. ' + response_message, true);
                }
                $(this_btn).removeClass('load');
            };

            var state = { num_to_import: products_to_import.length, import_cnt: 0, import_error_cnt: 0 };
            b2wl_js_post_to_woocomerce(products_to_import, state, on_load);

            return false;
        });


        $('#b2wl-import-actions .action-with-check select').change(function () {
            var ids = [];
            $('.b2wl-product-import-list .select :checkbox:checked:not(:disabled)').each(function () {
                ids.push($(this).val());
            });

            if ($(this).val() === 'remove') {

                $('.modal-confirm').confirm_modal({
                    title: 'Remove ' + $('.b2wl-product-import-list .select :checkbox:checked').length + ' products',
                    body: 'Are you sure you want to remove ' + $('.b2wl-product-import-list .select :checkbox:checked').length + ' products from your import list?',
                    yes: function () {
                        $('#b2wl-import-actions .action-with-check').addClass('load');
                        $("#b2wl-import-actions .action-with-check select").prop('disabled', true);

                        var data = { 'action': 'b2wl_delete_import_products', 'ids': ids };
                        jQuery.post(ajaxurl, data).done(function (response) {
                            var json = jQuery.parseJSON(response);
                            if (json.state !== 'ok') {
                                console.log(json);
                            }
                            $('#b2wl-import-actions .action-with-check').removeClass('load');
                            $("#b2wl-import-actions .action-with-check select").prop('disabled', false);
                            $('#b2wl-import-actions .action-with-check select').val(0);
                            $.each(ids, function (i, item) {
                                $('.b2wl-product-import-list .select :checkbox[value="' + item + '"]').closest('.product').parents('.row').remove();
                            });
                            update_bulk_actions();

                            if ($('.b2wl-product-import-list').find('.product').length === 0) {
                                $('#b2wl-import-content').hide();
                                $('#b2wl-import-empty').show();
                            }

                        }).fail(function (xhr, status, error) {
                            console.log(error);
                            $('#b2wl-import-actions .action-with-check').removeClass('load');
                            $("#b2wl-import-actions .action-with-check select").prop('disabled', false);
                            $('#b2wl-import-actions .action-with-check select').val(0);
                        });
                    },
                    no: function () {
                        $('#b2wl-import-actions .action-with-check select').val(0);
                    }
                });


            } else if ($(this).val() === 'push') {
                $('.modal-confirm').confirm_modal({
                    title: 'Push ' + $('.b2wl-product-import-list .select :checkbox:checked').length + ' products',
                    body: 'Are you sure you want to push ' + $('.b2wl-product-import-list .select :checkbox:checked').length + ' products to woocommerce?',
                    yes: function () {
                        $('#b2wl-import-actions .action-with-check').addClass('load');
                        $("#b2wl-import-actions .action-with-check select").prop('disabled', true);

                        var products_to_import = [];
                        $.each(ids, function (i, item) {
                            products_to_import.push({ 'action': 'b2wl_push_product', 'id': item });
                        });

                        $('.b2wl-product-import-list .select :checkbox:checked:not(:disabled)').closest('.product').find('.post_import').addClass('load');

                        var on_load = function (id, response_state, response_message, state) {
                            if ((state.import_cnt + state.import_error_cnt) === state.num_to_import) {
                                $('#b2wl-import-actions .action-with-check').removeClass('load');
                                $("#b2wl-import-actions .action-with-check select").prop('disabled', false);
                                $('#b2wl-import-actions .action-with-check select').val(0);
                            }
                            if (response_state !== 'error') {
                                $('.b2wl-product-import-list .select :checkbox[value="' + id + '"]').closest('.product').parents('.row').remove();
                            }

                            if ($('.b2wl-product-import-list').find('.product').length === 0) {
                                $('#b2wl-import-content').hide();
                                $('#b2wl-import-empty').show();
                            }
                        };


                        var state = { num_to_import: products_to_import.length, import_cnt: 0, import_error_cnt: 0 };

                        b2wl_js_post_to_woocomerce(products_to_import, state, on_load);
                        b2wl_js_post_to_woocomerce(products_to_import, state, on_load);
                    },
                    no: function () {
                        $('#b2wl-import-actions .action-with-check select').val(0);
                    }
                });
            } else if ($(this).val() === 'link-category') {
                $(".set-category-dialog .categories").attr('data-link-type', 'selected');
                $(".set-category-dialog").addClass('opened');
                update_bulk_actions();
                $('#b2wl-import-actions .action-with-check select').val(0);
            }
        });

        $('#b2wl-import-actions .link_category_all').click(function () {
            $(".set-category-dialog .categories").attr('data-link-type', 'all');
            $(".set-category-dialog").addClass('opened');
            return false;
        });

        $('#b2wl-import-actions .delete_all').click(function () {
            var del_url = $(this).attr('href');
            $('.modal-confirm').confirm_modal({
                title: 'Remove all products',
                body: 'Are you sure you want to remove all products from your import list?',
                yes: function () {
                    window.location.href = del_url;
                }
            });
            return false;
        });

        $('#b2wl-import-actions .push_all').click(function () {
            var this_btn = $(this);
            $('.modal-confirm').confirm_modal({
                title: 'Push all products',
                body: 'Are you sure you want to push all products to woocommerce?',
                yes: function () {
                    $(this_btn).addClass('load');
                    $('.b2wl-product-import-list .select :checkbox:not(:disabled)').closest('.product').find('.post_import').addClass('load');

                    var data = { 'action': 'b2wl_get_all_products_to_import' };
                    jQuery.post(ajaxurl, data).done(function (response) {
                        var json = jQuery.parseJSON(response);
                        if (json.state !== 'ok') {
                            console.log(json);
                        }

                        var products_to_import = [];
                        $.each(json.ids, function (i, id) {
                            products_to_import.push({ 'action': 'b2wl_push_product', 'id': id });
                        });

                        var on_load = function (id, response_state, response_message, state) {
                            if ((state.import_cnt + state.import_error_cnt) === state.num_to_import) {
                                $(this_btn).removeClass('load');
                            }
                            if (response_state !== 'error') {
                                $('.b2wl-product-import-list .select :checkbox[value="' + id + '"]').closest('.product').parents('.row').remove();
                            } else {
                                $('.b2wl-product-import-list .select :checkbox[value="' + id + '"]').closest('.product').find('.post_import').removeClass('load');
                            }

                            if ($('.b2wl-product-import-list').find('.product').length === 0) {
                                $('#b2wl-import-content').hide();
                                $('#b2wl-import-empty').show();
                            }
                        };

                        var state = { num_to_import: products_to_import.length, import_cnt: 0, import_error_cnt: 0 };
                        b2wl_js_post_to_woocomerce(products_to_import, state, on_load);
                        b2wl_js_post_to_woocomerce(products_to_import, state, on_load);

                    }).fail(function (xhr, status, error) {
                        $(this_btn).removeClass('load');
                    });
                }
            });
            return false;
        });


        function b2wl_js_post_to_woocomerce(products_to_import, state, on_load_calback) {
            if (products_to_import.length > 0) {
                var data = products_to_import.shift();
                var product = $('.product[data-id="' + data.id + '"]');

                if (b2wl_need_update_product($(product).attr('data-id'))) {
                    b2wl_update_product(data.id, prepareProductUpdateData(product), function () {
                        jQuery.post(ajaxurl, data).done(function (response) {
                            var json = jQuery.parseJSON(response);
                            if (json.state !== 'ok') {
                                console.log(json);
                            }

                            if (json.state === 'error') {
                                state.import_error_cnt++;

                            } else {
                                state.import_cnt++;
                            }

                            if (on_load_calback) {
                                on_load_calback(data.id, json.state, json.message, state);
                            }

                            b2wl_js_post_to_woocomerce(products_to_import, state, on_load_calback);
                        }).fail(function (xhr, status, error) {
                            console.log(error);
                            state.import_error_cnt++;

                            if (on_load_calback) {
                                on_load_calback(data.id, 'error', 'request error', state);
                            }

                            b2wl_js_post_to_woocomerce(products_to_import, state, on_load_calback);
                        });

                    }, 0);
                } else {
                    jQuery.post(ajaxurl, data).done(function (response) {
                        var json = jQuery.parseJSON(response);
                        if (json.state !== 'ok') {
                            console.log(json);
                        }

                        if (json.state === 'error') {
                            state.import_error_cnt++;

                        } else {
                            state.import_cnt++;
                        }

                        if (on_load_calback) {
                            on_load_calback(data.id, json.state, json.message, state);
                        }

                        b2wl_js_post_to_woocomerce(products_to_import, state, on_load_calback);
                    }).fail(function (xhr, status, error) {
                        console.log(error);
                        state.import_error_cnt++;

                        if (on_load_calback) {
                            on_load_calback(data.id, 'error', 'request error', state);
                        }

                        b2wl_js_post_to_woocomerce(products_to_import, state, on_load_calback);
                    });
                }
            }
        }

        function prepareProductUpdateData(product, updateDescription = false) {
            var updateData = {
                title: $(product).find('input.title').val(),
                sku: $(product).find('input.sku').val(),
                tags: $(product).find('.tags').val(),
                type: $(product).find('.type').val(),
                status: $(product).find('.status').val(),
                categories: $(product).find('.categories').val(),
                attr_names: [],
                specs: []
            };
            $(product).find('input.attr-name').each(function () {
                updateData.attr_names.push({ id: $(this).attr('data-id'), value: $(this).val() });
            });
            if (updateDescription && typeof tinyMCE !== 'undefined') {
                updateData['description'] = encodeURIComponent(b2wl_tmce_getContent(updateData.id));
            }

            $(product).find('.specs-table tbody tr').each(function () {
                updateData.specs.push({ name: $(this).find('.spec-name input').val(), value: $(this).find('.spec-value input').val() });
            });

            if (updateData.specs.length === 0) {
                updateData.cleanSpecs = true;
            }

            return updateData;
        }

        $('.b2wl-content .product input.title, .b2wl-content .product input.sku').change(function () {
            var product = $(this).parents('.product');
            b2wl_update_product($(product).attr('data-id'), prepareProductUpdateData(product));
        });

        $(".b2wl-content .product .select2, .b2wl-content .product .select2-tags").on("select2:select", function (e) {
            var product = $(this).parents('.product');
            b2wl_update_product($(product).attr('data-id'), prepareProductUpdateData(product));
        });
        $(".b2wl-content .product .select2, .b2wl-content .product .select2-tags").on("select2:unselect", function (e) {
            var product = $(this).parents('.product');
            b2wl_update_product($(product).attr('data-id'), prepareProductUpdateData(product));
        });

        $('.b2wl-content .product input.attr-name').change(function () {
            var product = $(this).parents('.product');
            b2wl_update_product($(product).attr('data-id'), prepareProductUpdateData(product));
        });

        $(".b2wl-content .product .specs-table tbody").on("change", "input", function () {
            var product = $(this).parents('.product');
            b2wl_update_product($(product).attr('data-id'), prepareProductUpdateData(product));
        });

        $('.b2wl-content .variants-table .var_data input[type="text"]').change(function () {
            var product = $(this).parents('.product');
            b2wl_calc_profit(jQuery(product).attr('data-id'));

            let variations = [];
            $(product).find('.variants-table tbody tr').each(function () {
                var variation = { variation_id: $(this).attr('data-id'), sku: $(this).find('.sku').val(), quantity: $(this).find('.quantity').val(), price: $(this).find('.price').val(), regular_price: $(this).find('.regular_price').val(), attributes: [] };
                $(this).find('input.attr').each(function () {
                    variation.attributes.push({ id: $(this).attr('data-id'), value: $(this).val() });
                });
                variations.push(variation);
            });
            b2wl_update_product($(product).attr('data-id'), { variations }, response => {
                if (response.state === "ok" && response.new_attr_mapping) {
                    $.each(response.new_attr_mapping, function (i, a) {
                        $('[data-id="' + a.variation_id + '"] input[data-id="' + a.old_attr_id + '"]').attr('data-id', a.new_attr_id);
                    });
                }
            });
        });

        jQuery(".wp-editor-container textarea").change(function (e) {
            b2wl_update_product($(this).attr('id'), { description: encodeURIComponent($(this).val()) })
        });

        $('.b2wl-content .price-edit-selector .dropdown-menu a').click(function () {
            if ($(this).hasClass('set-new-value')) {
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').attr('placeholder', 'Enter Value');
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data({ type: 'value' });
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').val('');
            } else if ($(this).hasClass('multiply-by-value')) {
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').attr('placeholder', 'Enter Multiplier');
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data({ type: 'multiplier' });
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').val('');
            } else if ($(this).hasClass('set-new-quantity')) {
                $(this).parents('.price-edit-selector').removeClass('random-value');
                $(this).parents('.price-edit-selector').addClass('set-new-quantity');
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data({ type: 'quantity-value' });
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"].simple-value').val('');
            } else if ($(this).hasClass('random-value')) {
                $(this).parents('.price-edit-selector').removeClass('set-new-quantity');
                $(this).parents('.price-edit-selector').addClass('random-value');
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data({ type: 'quantity-random' });
            } else if ($(this).hasClass('rename-attr-value')) {
                const $attrSelector = $(this).parents('td').find('.price-box-top select')
                const attr_id = $(this).parents('td').attr('data-attr-id');
                $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').val('');
                $attrSelector.find('option').remove();
                $(this).parents('.variants-table').find('td[data-attr-id="' + attr_id + '"] input').each(function () {
                    const val = $(this).val()
                    if (val && $attrSelector.find('option[value="' + val + '"]').length == 0) {
                        $attrSelector.append('<option value="' + val + '">' + val + '</option>')
                    }
                });
            }


            $('.price-edit-selector').find('.price-box-top').hide();
            $(this).parents('.price-edit-selector').find('.price-box-top').toggle();
        });

        $('.b2wl-content .price-edit-selector .price-box-top .apply').click(function () {
            if ($(this).parents('.price-edit-selector').hasClass('rename-attr')) {
                const attr_id = $(this).parents('td').attr('data-attr-id')
                const val = $(this).parents('.price-edit-selector').find('select').val();
                const newVal = $(this).parents('.price-edit-selector').find('input').val();
                if (newVal) {
                    $(this).parents('.variants-table').find('td[data-attr-id="' + attr_id + '"] input').each(function () {
                        if ($(this).val() == val) {
                            $(this).val(newVal)
                        }
                    });
                }
                $(this).parents('.variants-table').find('.var_data input[type="text"]').first().trigger('change');
            } else {
                var new_value = -1, new_from = -1, new_from = -1, type = '';
                if ($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data().type === 'value') {
                    type = $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data().type;
                    new_value = parseFloat($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').val());
                } else if ($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data().type === 'multiplier') {
                    type = $(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data().type;
                    new_value = parseFloat($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').val());
                } else if ($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data().type === 'quantity-value') {
                    new_value = parseInt($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').val());
                } else if ($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"]').data().type === 'quantity-random') {
                    new_from = parseInt($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"].random-from').val());
                    new_to = parseInt($(this).parents('.price-edit-selector').find('.price-box-top input[type="text"].random-to').val());
                    new_value = (new_from > 0 && new_to > 0) ? 1 : 0;
                }
                if (new_value > 0) {
                    if ($(this).parents('.price-edit-selector').hasClass('edit-regular-price')) {
                        $(this).parents('.variants-table').find('input[type="text"].regular_price').each(function () {
                            var min_price = $(this).parents('tr').find('input[type="text"].price').val();
                            var cur_value = Number(((type == 'multiplier') ? parseFloat($(this).val()) * new_value : new_value).toFixed(2));
                            $(this).val(min_price < cur_value ? cur_value : min_price);
                        });
                    } else if ($(this).parents('.price-edit-selector').hasClass('edit-price')) {
                        $(this).parents('.variants-table').find('input[type="text"].price').each(function () {
                            var max_price = $(this).parents('tr').find('input[type="text"].regular_price').val();
                            var cur_value = Number(((type == 'multiplier') ? parseFloat($(this).val()) * new_value : new_value).toFixed(2));
                            $(this).val(max_price > cur_value ? cur_value : max_price);
                        });
                    } else if ($(this).parents('.price-edit-selector').hasClass('edit-quantity')) {
                        var edit_selector = $(this).parents('.price-edit-selector');
                        $(this).parents('.variants-table').find('input[type="text"].quantity').each(function () {
                            if ($(edit_selector).hasClass('set-new-quantity')) {
                                $(this).val(new_value);
                            } else if ($(edit_selector).hasClass('random-value')) {
                                if (new_from > new_to) {
                                    var tmp = new_from;
                                    new_from = new_to;
                                    new_to = tmp;
                                }
                                $(this).val(Math.round(Math.random() * (new_to - new_from)) + new_from);
                            }
                        });
                    }

                    if ($(this).parents('.variants-table').find('.var_data input[type="text"]').length > 0) {
                        $(this).parents('.variants-table').find('.var_data input[type="text"]').first().trigger('change');
                    }
                }
            }



            $(this).parents('.price-box-top').hide();
        });

        $('.b2wl-content .price-edit-selector .price-box-top .close').click(function () {
            $(this).parents('.price-box-top').hide();
        });

        $('.b2wl-content .variants-actions .disable-var-price-change').change(function () {
            var $product = $(this).closest('.product');
            var disable_var_price_change = $(this).is(':checked') ? 1 : 0;
            var skip_vars = [];
            $($product).find('.variants-table .check-var').not(':checked').each(function () {
                skip_vars.push($(this).closest('tr').attr('data-id'));
            });
            b2wl_update_product($product.attr('data-id'), { disable_var_price_change: disable_var_price_change });
        });

        $('.b2wl-content .variants-actions .disable-var-quantity-change').change(function () {
            var $product = $(this).closest('.product');
            var disable_var_quantity_change = $(this).is(':checked') ? 1 : 0;
            var skip_vars = [];
            $($product).find('.variants-table .check-var').not(':checked').each(function () {
                skip_vars.push($(this).closest('tr').attr('data-id'));
            });
            b2wl_update_product($product.attr('data-id'), { disable_var_quantity_change: disable_var_quantity_change });
        });

        // load external images -->
        var b2wl_external_images_page_size = 1000;

        function b2wl_calc_external_images(on_load_calback) {
            var data = { 'action': 'b2wl_calc_external_images', 'page_size': b2wl_external_images_page_size };
            jQuery.post(ajaxurl, data).done(function (response) {
                var json = jQuery.parseJSON(response);

                var block_data = $('#b2wl_load_external_image_block').data();

                on_load_calback(json.ids, block_data);

            }).fail(function (xhr, status, error) {
                console.log(error);
            });
        }


        var b2wl_calc_external_images_calback = function (ids, block_data) {
            images_to_load = [];
            jQuery.each(ids, function (i, id) {
                images_to_load.push({ 'action': 'b2wl_load_external_image', 'id': id });
            });

            var on_load = function (state) {
                $("#b2wl_load_external_image_progress").html(b2wl_sprintf(b2wl_common_data.lang.process_loading_d_of_d_erros_d, block_data.ok + state.import_cnt, block_data.total, block_data.error + state.import_error_cnt));

                if ((state.import_cnt + state.import_error_cnt) === state.num_to_import) {
                    block_data.ok += state.import_cnt;
                    block_data.error += state.import_error_cnt;

                    $('#b2wl_load_external_image_block').data(block_data);

                    if ((block_data.ok + block_data.error) < block_data.total) {
                        b2wl_calc_external_images(b2wl_calc_external_images_calback);
                    } else {
                        location.reload();
                    }
                }
            };

            var state = { num_to_import: images_to_load.length, import_cnt: 0, import_error_cnt: 0 };
            b2wl_load_external_image(images_to_load, state, on_load);
        };

        function b2wl_load_external_image(images_to_load, state, on_load) {
            if (images_to_load.length > 0) {
                var data = images_to_load.shift();

                jQuery.post(ajaxurl, data).done(function (response) {
                    var json = jQuery.parseJSON(response);
                    if (json.state !== 'ok') {
                        console.log(json);
                    }

                    if (json.state === 'error') {
                        state.import_error_cnt++;

                    } else {
                        state.import_cnt++;
                    }

                    if (on_load) {
                        on_load(state);
                    }

                    b2wl_load_external_image(images_to_load, state, on_load);
                }).fail(function (xhr, status, error) {
                    console.log(error);
                    state.import_error_cnt++;

                    if (on_load) {
                        on_load(state);
                    }

                    b2wl_load_external_image(images_to_load, state, on_load);
                });
            }
        }



        $('#b2wl_load_external_image_block .load-images').click(function () {
            $('#b2wl_load_external_image_block .load-images').attr('disabled', 'disabled');
            var block_data = $('#b2wl_load_external_image_block').data();
            $("#b2wl_load_external_image_progress").html(b2wl_sprintf(b2wl_common_data.lang.process_loading_d_of_d_erros_d, 0, block_data.total, 0));
            b2wl_calc_external_images(b2wl_calc_external_images_calback);
        });

        $('.set-category-dialog .modal-footer .btn').click(function () {
            if ($(this).hasClass('no-btn')) {
                $(this).parents(".modal-overlay").removeClass('opened');
            } else if ($(this).hasClass('yes-btn')) {
                var new_categories = $(this).parents(".modal-overlay").find('.categories').val();
                var link_type = $(this).parents(".modal-overlay").find('.categories').attr('data-link-type');

                if (new_categories) {
                    if (link_type === 'all') {
                        var ids = 'all';
                        $('.b2wl-product-import-list .product select.categories').each(function () {
                            $(this).val(new_categories).trigger('change');
                        });
                    } else {
                        var ids = [];
                        $('.b2wl-product-import-list .select :checkbox:checked:not(:disabled)').each(function () {
                            $(this).parents('.product').find('select.categories').val(new_categories).trigger('change');
                            ids.push($(this).val());
                        });
                    }

                    jQuery.post(ajaxurl, { 'action': 'b2wl_link_to_category', 'categories': new_categories, 'ids': ids }).fail(function (xhr, status, error) { console.log(error); });
                }
                $(this).parents(".modal-overlay").removeClass('opened');
            }
            return false;
        });

        // init load button
        if ($("#b2wl_use_external_image_urls").length > 0 && !$("#b2wl_use_external_image_urls").is(':checked')) {
            $('#b2wl_load_external_image_block').data({ total: 0, ok: 0, error: 0 });
            var data = { 'action': 'b2wl_calc_external_images_count' };
            jQuery.post(ajaxurl, data).done(function (response) {
                var json = jQuery.parseJSON(response);

                $('#b2wl_load_external_image_block').data({ total: json.total_images, ok: 0, error: 0 });
                if (json.total_images > 0) {
                    $('#b2wl_load_external_image_block .load-images').removeAttr('disabled');
                    $('#b2wl_load_external_image_block .load-images').val(b2wl_sprintf(b2wl_common_data.lang.load_button_text, json.total_images));
                } else {
                    $("#b2wl_load_external_image_progress").html(b2wl_common_data.lang.all_images_loaded_text);
                }

            }).fail(function (xhr, status, error) {
                console.log(error);
            });
        }
        // <-- load external images

        $('[data-toggle="popover-hover"]').popover &&
            $('[data-toggle="popover-hover"]').popover({
                html: true,
                trigger: 'hover',
                placement: 'right',
                content: function () { return '<img src="' + $(this).data('img') + '" />'; }
            });

        $('#b2wl-import-filter form select[name="o"]').change(function () {
            $(this).parents('form').submit();
        });

        $('.b2wl-content .specs-actions .add-spec').click(function () {
            $('.b2wl-content .specs-table tbody').append('<tr><td class="column-handle ui-sortable-handle"></td><td class="spec-name"><input type="text" class="form-control" value=""></td><td class="spec-value"><input type="text" class="form-control" value=""></td><td class="spec-actions"><a href="#" class="del-spec close-icon"></a></td></tr>');
            return false;
        });

        $(".b2wl-content").on("click", ".spec-actions .del-spec", function () {
            var this_spec = this;
            $('.modal-confirm').confirm_modal({
                title: 'Remove spec',
                body: 'Are you sure you want to remove this spec?',
                yes: function () {
                    var product = $(this_spec).parents('.product');

                    $(this_spec).parents('tr').remove();

                    if ($(product).find('.specs-table .spec-name input').length > 0) {
                        $(product).find('.specs-table .spec-name input').change();
                    } else {
                        b2wl_update_product($(product).attr('data-id'), prepareProductUpdateData(product));
                    }
                }
            });
            return false
        });

        $(".check-spec-delete").click(function () {
            if ($(this).is(':checked')) {
                $(this).parents('tr').addClass('checked-spec');
            } else {
                $(this).parents('tr').removeClass('checked-spec');
            }
        });
        $(".b2wl-content").on("click", ".btn-delete-specs", function () {
            var this_spec = this;

            $('.modal-confirm').confirm_modal({
                title: 'Remove spec',
                body: 'Are you sure you want to remove selected specs?',
                yes: function () {
                    var product = $(this_spec).parents('.product');
                    var product_id = $(this_spec).parents('.product').data('id');
                    $('.product[data-id="' + product_id + '"]').find('.specs-table tr.checked-spec').remove();
                    if ($(product).find('.specs-table .spec-name input').length > 0) {
                        $(product).find('.specs-table .spec-name input').change();
                    } else {
                        b2wl_update_product($(product).attr('data-id'), prepareProductUpdateData(product));
                    }
                }
            });

            return false
        });

    });

})(jQuery, window, document);



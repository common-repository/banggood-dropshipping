
<div class="panel panel-primary mt20" id="phrase_list">
    <div class="panel-heading">
        <h3 class="panel-title"><?php _ex('Phrase Filtering (case sensitive)', 'Setting title', 'bng2woo-lite');?></h3>
        <span class="pull-right">
            <a class="disabled" style="display: none;"><?php _e('You have unsaved changes', 'bng2woo-lite');?></a>
            <a href="#" class="apply-phrase-rules btn"><?php _e('Apply Filter to your Shop', 'bng2woo-lite');?></a></span>
    </div>


    <div class="panel-body">
        <div class="panel panel-default" id="b2wl-panel-info" style="display: none;">
            <div class="panel-heading"><?php _e('Applying filter progress', 'bng2woo-lite');?>  <button type="button" class="close" data-target="#b2wl-panel-info" data-dismiss="alert"> <span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close');?></span>

            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        Reviews
                        <div class="progress reviews-progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 4em;">
                                wait..
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 pb20">
                <strong><?php _e('Phrase', 'bng2woo-lite');?></strong>
            </div>
            <div class="col-md-8 pb20">
                <strong><?php _e('Replacement', 'bng2woo-lite');?></strong>
            </div>
        </div>
        <?php foreach ($phrases as $ind => $phrase): ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group input-block no-margin">
                        <input type="text" value="<?php echo esc_attr($phrase->phrase); ?>" class="form-control small-input b2wl_phrase" placeholder="<?php _e('some phrase or word', 'bng2woo-lite');?>" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group input-block no-margin">
                        <input type="text" value="<?php echo esc_attr($phrase->phrase_replace); ?>" class="form-control small-input b2wl_phrase_replace" placeholder="<?php _e('replacement or empty', 'bng2woo-lite');?>" />
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn--transparent delete">
                        <svg class="icon-cross">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endforeach;?>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group input-block no-margin">
                    <input type="text" class="form-control small-input b2wl_phrase" placeholder="<?php _e('some phrase or word', 'bng2woo-lite');?>" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-block no-margin">
                    <input type="text" class="form-control small-input b2wl_phrase_replace" placeholder="<?php _e('replacement or empty', 'bng2woo-lite');?>" />
                </div>
            </div>
            <div class="col-md-4">
                <button class="btn btn--transparent delete" style="display:none;">
                    <svg class="icon-cross">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use>
                    </svg>
                </button>
            </div>
        </div>

    </div>

</div>
<div class="panel small-padding margin-small-top panel-danger" style="display: none;">
    <div class="panel-body">
        <div class="container-flex flex-between">
            <div class="container-flex">
                <div class="svg-container no-shrink">
                    <svg class="icon-danger-circle margin-small-right">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-danger-circle"></use>
                    </svg>
                </div>
                <div class="ml5 mr10">
                    <div class="content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row pt20 border-top">
        <div class="col-sm-12">
            <input class="btn btn-success" id="save-phrases"  type="submit" value="<?php _e('Save settings', 'bng2woo-lite');?>"/>
        </div>
    </div>
</div>

<div class="modal-overlay modal-apply-phrases">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('Apply Filter to your Shop', 'bng2woo-lite');?></h3>
            <a class="modal-btn-close" href="#"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></a>
        </div>
        <div class="modal-body">
            <label><?php _e('Select the update type', 'bng2woo-lite');?></label>
            <div style="padding-bottom: 20px;">
                <div class="type btn-group" role="group">
                    <button type="button" class="btn btn-default" value="products"><?php _ex('Products', 'Apply Phrases', 'bng2woo-lite');?></button>
                    <?php /*
<button type="button" class="btn btn-default" value="shippings"><?php _ex('Shipping methods', 'Apply Phrases', 'bng2woo-lite'); ?></button>
 */?>
                    <button type="button" class="btn btn-default" value="all_types"><?php _ex('All', 'Apply Phrases', 'bng2woo-lite');?></button>
                </div>
            </div>
            <div class="scope">
                <label><?php _e('Select the update scope', 'bng2woo-lite');?></label>
                <div>
                    <div class="scope btn-group" role="group">
                        <button type="button" class="btn btn-default" value="shop"><?php _ex('Shop', 'Apply Phrases', 'bng2woo-lite');?></button>
                        <button type="button" class="btn btn-default" value="import"><?php _ex('Import List', 'Apply Phrases', 'bng2woo-lite');?></button>
                        <button type="button" class="btn btn-default" value="all"><?php _ex('Shop and Import List', 'Apply Phrases', 'bng2woo-lite');?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default close-btn" type="button"><?php _e('Close');?></button>
            <button class="btn btn-success apply-btn" type="button"><?php _e('Apply');?></button>
        </div>
    </div>
</div>



<script>
    (function ($) {

        var set_progress_bar_value = function (c, v) {
            jQuery('#b2wl-panel-info ' + c + ' .progress-bar')
                    .css('width', v + '%')
                    .attr('aria-valuenow', v)
                    .html(v + '%');
        }

        var get_status_filter = function (show) {

            var data = {action: 'b2wl_get_status_apply_phrase_rules'};
            jQuery.post(ajaxurl, data).done(function (response) {
                var json = $.parseJSON(response);
                if (json.state === 'error') {
                    console.log(json);
                } else {


                    if (typeof json.review_valuenow !== 'undefined') {

                        jQuery('#b2wl-panel-info').fadeIn(400);
                        set_progress_bar_value('.reviews-progress', json.review_valuenow);

                        setTimeout(get_status_filter, 1000);
                    }
                    else if (typeof show !== 'undefined') {
                        jQuery('#b2wl-panel-info').fadeIn(400);
                        setTimeout(function () {
                            get_status_filter(true)
                        }, 1000);
                    }
                    else {
                        //jQuery('.panel-info').fadeOut(400);
                    }

                    if (typeof json.review_valuenow == 'undefined') {
                        set_progress_bar_value('.reviews-progress', 100);
                    }
                }

            }).fail(function (xhr, status, error) {
                show_notification('Get status of filters failed.', true);
            });

        }

        //  get_status_filter();

        $(".apply-phrase-rules").on("click", function () {
            $(".modal-apply-phrases .btn-group").each(function () {
                $(this).find('.btn').removeClass('btn-info').removeClass('active').addClass('btn-default');
                $(this).find('.btn:first').removeClass('btn-default').addClass('btn-info').addClass('active');
                $(this).data({value: $(this).find('.btn:first').val()});
            });

            $(".modal-apply-phrases .scope").show();
            $(".modal-apply-phrases").addClass('opened');
            return false;
        });

        $(".modal-apply-phrases .btn-group .btn").on("click", function () {

            if ($(this).val() == 'reviews' || $(this).val() == 'shippings')
                $(".modal-apply-phrases .scope").hide();

            else if ($(this).val() == 'products' || $(this).val() == 'all_types')
                $(".modal-apply-phrases .scope").show();

            $(this).parents('.btn-group').find('.btn').removeClass('btn-info').removeClass('active').addClass('btn-default');
            $(this).removeClass('btn-default').addClass('btn-info').addClass('active');
            $(this).parents('.btn-group').data({value: $(this).val()});
        });

        $(".modal-apply-phrases .close-btn").on("click", function () {
            $(".modal-apply-phrases").removeClass('opened');
            return false;
        });

        $(".modal-apply-phrases .apply-btn").on("click", function () {
            $(".modal-apply-phrases").removeClass('opened');

            //  get_status_filter(true);

            var data = {action: 'b2wl_apply_phrase_rules', type: $(".modal-apply-phrases .btn-group.type").data().value, scope: $(".modal-apply-phrases .btn-group.scope").data().value};
            jQuery.post(ajaxurl, data).done(function (response) {
                show_notification('Applying filter to your Shop');
            }).fail(function (xhr, status, error) {
                show_notification('Applying filter failed.', true);
            });

            return false;
        });

        function check_phrases() {
            var empty_check = true;
            $('#phrase_list > .panel-body .has-error').removeClass('has-error');

            $('#phrase_list > .panel-body .row:gt(0)').each(function () {
                if (!$(this).is(":last-child") && $(this).find(".b2wl_phrase").length > 0 && $.trim($(this).find(".b2wl_phrase").val()) == '') {
                    $(this).find(".b2wl_phrase").addClass('has-error');
                    empty_check = false;
                }
            });



            $('.panel-danger').hide();
            if (!empty_check) {
                $('.panel-danger .content').html("Please fill out Phrase fields");
                $('.panel-danger').show();
            }

            return empty_check;

        }

        function set_last_phrase_row_enability(show) {
            var row = $('#phrase_list > .panel-body .row:last-child');

            if (show) {
                row.find('.b2wl_phrase_replace').removeClass('opacity50');
                row.find('.b2wl_phrase_replace').prop('disabled', false);

            } else {
                row.find('.b2wl_phrase_replace').removeClass('opacity50').addClass('opacity50');

                row.find('.b2wl_phrase_replace').prop('disabled', true);

            }


        }

        function add_phrase_row(this_row) {
            var row = $(this_row).parents('.panel-body').children('.row:last-child'),
                    new_row = row.clone();

            new_row.find('.b2wl_phrase').val('');
            new_row.find('.b2wl_phrase_replace').val('');
            new_row.find('.delete').hide();

            set_last_phrase_row_enability(true);
            $(this_row).parents('.panel-body').append(new_row);
            set_last_phrase_row_enability(false);

            row.find('.delete').show();
        }


        var settings_changed = false;

        $("#phrase_list > .panel-body").change(function () {
            if (!settings_changed) {
                settings_changed = true;

                $('a.apply-phrase-rules').hide();
                $('a.apply-phrase-rules').prev().show();

            }
        });


        set_last_phrase_row_enability(false);

        var keyup_timer = false;

        $('#phrase_list > .panel-body').on('keyup', 'input[type="text"]', function () {
            var this_row = $(this).parents('.row');
            if (keyup_timer) {
                clearTimeout(keyup_timer);
            }
            keyup_timer = setTimeout(function () {

                if (check_phrases() && $.trim($(this_row).parents('.panel-body').find(".row:last-child .b2wl_phrase").val()) != '') {

                    add_phrase_row(this_row);


                }
            }, 1000);

            //$(this).removeClass('error_input');
        });

        $('#phrase_list > .panel-body').on('click', '.delete', function () {
            if ($(this).parents('.row').is(":eq(1)") && $(this).parents('.panel-body').find('.row').length < 3) {
                //first action: empty first phrase row
                var row = $(this).parents('.row:eq(1)');
                row.find('input[type="text"]').val('');
            } else if ($(this).parents('.row').is(":last-child")) {
                //last action must be empty
            } else {
                $(this).trigger('change');
                $(this).parents('.row').remove();
            }

            check_phrases();

            return false;
        });

        if (jQuery.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip({"placement": "top"});
        }

        $('#save-phrases').on('click', function () {
            if ($(this).find('.has-error').length > 0)
                return false;

            var data = {'action': 'b2wl_update_phrase_rules', 'phrases': []};

            $('#phrase_list > .panel-body .row').each(function () {
                if (!$(this).is(":last-child") && !$(this).is(":first-child")) {
                    var rule = {'phrase': $(this).find('.b2wl_phrase').val(),
                        'phrase_replace': $(this).find('.b2wl_phrase_replace').val()
                    };
                    data.phrases.push(rule);
                }
            });

            jQuery.post(ajaxurl, data).done(function (response) {
                show_notification('Saved successfully.');
                var json = jQuery.parseJSON(response);

                settings_changed = false;
                $('a.apply-phrase-rules').show();
                $('a.apply-phrase-rules').prev().hide();

            }).fail(function (xhr, status, error) {
                show_notification('Save failed.', true);
            });

            return false;

        });

    })(jQuery);




</script>

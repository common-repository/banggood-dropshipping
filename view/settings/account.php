<form method="post">
    <input type="hidden" name="setting_form" value="1"/>
    <div class="account_options <?php if ($account->custom_account): ?> custom_account<?php endif;?> account_type_<?php echo $account->account_type; ?>">
        <div class="panel panel-primary mt20">
            <div class="panel-heading">
                <h3 class="display-inline"><?php _ex('Account settings', 'Setting title', 'bng2woo-lite');?></h3>
            </div>
            <div class="panel-body">
                <div class="row account_fields account_fields_default">
                    <div class="col-sm-4">
                        <label>
                            <strong><?php _e('Appid', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('Appid provided by Banggood Open Platform', 'setting description', 'bng2woo-lite');?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <input type="text" class="form-control small-input" id="b2wl_appkey" name="b2wl_appkey" value="<?php echo esc_attr(isset($account->account_data['banggood']['appkey']) ? $account->account_data['banggood']['appkey'] : ''); ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row account_fields account_fields_default">
                    <div class="col-sm-4">
                        <label>
                            <strong><?php _e('AppSecret', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('AppSecret provided byBanggood Open Platform', 'setting description', 'bng2woo-lite');?>"></div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group input-block no-margin">
                            <input type="text" class="form-control small-input" id="b2wl_secretkey" name="b2wl_secretkey" value="<?php echo esc_attr(isset($account->account_data['banggood']['secretkey']) ? $account->account_data['banggood']['secretkey'] : ''); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="_b2wfo b2wl-info"><div>This feature is available in the full plugin version.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
                <div class="row _b2wfv">
                    <div class="col-xs-12 col-sm-4">
                        <label>
                            <strong><?php esc_html_e('Configure affiliate links', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('You can set up affiliate links using one of supported platform.', 'setting description', 'bng2woo-lite'); ?>"></div>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <div class="form-group input-block no-margin clearfix">
                            <input type="checkbox" class="form-control float-left mr20" id="b2wl_use_custom_account" name="b2wl_use_custom_account" value="yes" <?php if ($account->custom_account): ?>checked<?php endif;?>/>
                            <div class="default_account">
                                <?php esc_html_e('You are using default links', 'bng2woo-lite');?>
                            </div>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row pt20 border-top">
            <div class="col-sm-12">
                <input class="btn btn-success js-main-submit" type="submit" value="<?php _e('Save settings', 'bng2woo-lite');?>"/>
            </div>
        </div>
    </div>
</form>

<script>
    (function ($) {
        if(jQuery.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip({"placement": "top"}); }
        
    })(jQuery);
</script>

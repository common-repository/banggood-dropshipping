<div class="panel panel-primary mt20">
    <?php if (isset($api_key)): ?>
        <form method="post">
            <input type="hidden" name="b2wl_api_key" value="<?php echo $api_key["id"]; ?>"/>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 mb20">
                        <a class="btn" href="<?php echo admin_url('admin.php?page=b2wl_setting&subpage=chrome_api'); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span><?php esc_html_e('Back to list', 'bng2woo-lite');?></a>
                    </div>
                    <div class="col-xs-12 form-group input-block no-margin clearfix" style="display: flex;align-items: center;">
                        <div style="width:100px">
                            <label for="b2wl_api_key_name">
                                <strong><?php esc_html_e('Name', 'bng2woo-lite');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('Friendly name for identifying this key.', 'setting description', 'bng2woo-lite'); ?>"></div>
                        </div>
                        <div style="flex:1">
                            <input type="text" class="form-control medium-input" id="b2wl_api_key_name" name="b2wl_api_key_name" value="<?php echo $api_key["name"]; ?>"/>
                        </div>
                    </div>

                    <div class="col-xs-12 form-group input-block no-margin clearfix" style="display: flex;align-items: center;"">
                        <div style="width:100px">
                            <label>
                                <strong><?php esc_html_e('URL', 'bng2woo-lite');?></strong>
                            </label>
                            <div class="info-box" data-toggle="tooltip" title="<?php echo esc_html_x('Use this URL in your b2w chrome extension settings.', 'setting description', 'bng2woo-lite'); ?>"></div>
                        </div>
                        <div id="<?php echo $api_key["id"]; ?>" style="flex:1">
                            <input type="text" readonly class="form-control medium-input" id="b2wl_api_key_url_<?php echo $api_key["id"]; ?>" name="b2wl_api_key_url" value="<?php echo site_url("?b2w-key=" . $api_key["id"]); ?>"/>
                            <a class="btn b2wl_api_key_url_copy" href="#"><span class="dashicons dashicons-admin-page"></span><?php esc_html_e('Copy to clipboard', 'bng2woo-lite');?></a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="container-fluid">
                <div class="row pt20 border-top">
                    <div class="col-sm-12">
                        <input class="btn btn-success js-key-submit" type="submit" value="<?php esc_html_e('Save changes', 'bng2woo-lite');?>"/>
                        <?php if (!$is_new_api_key): ?><a href="<?php echo admin_url('admin.php?page=b2wl_setting&subpage=chrome_api&delete-key=' . $api_key["id"]); ?>" class="btn btn-remove b2wl-api-key-delete"/><?php esc_html_e('Revoke key', 'bng2woo-lite');?></a><?php endif;?>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-12 vertical-center">
                    <h3 class="display-inline"><?php echo esc_html_x('API keys', 'Setting title', 'bng2woo-lite'); ?></h3>
                    <a class="btn btn-primary ml20" href="<?php echo admin_url('admin.php?page=b2wl_setting&subpage=chrome_api&edit-key'); ?>"><?php esc_html_e('Add key', 'bng2woo-lite');?></a>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php foreach ($api_keys as $api_key): ?>
                <div class="row pt20 border-bottom">
                    <div class="col-sm-12 b2wl-row-with-actions">
                        <div class="input-block no-margin clearfix vertical-center">
                            <b><?php echo $api_key['name']; ?></b>
                            <div id="<?php echo $api_key["id"]; ?>" class="ml20 vertical-center" style="min-width:520px;">
                                <input type="text" readonly class="form-control medium-input" id="b2wl_api_key_url_<?php echo $api_key["id"]; ?>" name="b2wl_api_key_url" value="<?php echo site_url("?b2w-key=" . $api_key["id"]); ?>"/>
                                <a class="btn b2wl_api_key_url_copy" href="#"><span class="dashicons dashicons-admin-page"></span><?php esc_html_e('Copy to clipboard', 'bng2woo-lite');?></a>
                            </div>
                        </div>
                        <div class="b2wl-row-actions">
                            <span>KEY: <?php echo $api_key['id']; ?></span> |
                            <a class="" href="<?php echo admin_url('admin.php?page=b2wl_setting&subpage=chrome_api&edit-key=' . $api_key["id"]); ?>"><?php esc_html_e('View/Edit', 'bng2woo-lite');?></a> |
                            <a class="btn-remove b2wl-api-key-delete" href="<?php echo admin_url('admin.php?page=b2wl_setting&subpage=chrome_api&delete-key=' . $api_key["id"]); ?>"><?php esc_html_e('Revoke key', 'bng2woo-lite');?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        </div>

    <?php endif;?>

</div>


<script>
    (function ($) {
        $(".b2wl_api_key_url_copy").click(function () {
            var copyText = document.getElementById("b2wl_api_key_url_"+$(this).parent().attr('id'));
            copyText.select();
            document.execCommand("copy");
            return false;
        });

        $(".b2wl-api-key-delete").click(function () {
            return confirm('Are you sure you want to Revoke the key');
        });
    })(jQuery);
</script>

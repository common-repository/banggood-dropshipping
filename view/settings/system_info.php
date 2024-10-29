<?php
$write_info_log = b2wl_get_setting('write_info_log');
$server_ping = B2WL_SystemInfo::server_ping();

$server_ping_str = ($server_ping['state'] !== 'ok' ? '<span class="error">ERROR</span>' : '<span class="ok">' . esc_attr($server_ping['message']) . '</span>');
if ($server_ping['state'] !== 'ok') {
    $server_ping_str .= '<div class="info-box" data-toggle="tooltip" title="' . esc_attr($server_ping['message']) . '"></div>';
}
?>

<form method="post">
    <input type="hidden" name="setting_form" value="1"/>
    <div class="system_info">
        <div class="panel panel-primary mt20">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label for="b2wl_write_info_log">
                            <strong><?php _e('Write bng2woo-lite logs', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _e('Write bng2woo-lite logs', 'bng2woo-lite');?>"></div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="form-group input-block no-margin">
                            <input type="checkbox" class="form-control" id="b2wl_write_info_log" name="b2wl_write_info_log" value="yes" <?php if ($write_info_log): ?>checked<?php endif;?>/>
                            <?php if ($write_info_log): ?>
                                <div><?php if (file_exists(B2WL_Logs::getInstance()->log_path())): ?><a target="_blank" href="<?php echo esc_url(B2WL_Logs::getInstance()->log_url()); ?>">Open log file</a> | <?php endif;?>
                                <a class="b2wl-clean-log" href="#">Delete log file</a></div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label>
                            <strong><?php _e('External IP', 'bng2woo-lite');?></strong>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="form-group input-block no-margin clearfix">
                            <?php echo $server_ping_str; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label>
                            <strong><?php _e('Php version', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('Php version', 'setting description', 'bng2woo-lite');?>"></div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="form-group input-block no-margin clearfix">
                            <?php
$result = B2WL_SystemInfo::php_check();
echo ($result['state'] !== 'ok' ? '<span class="error">ERROR</span>' : '<span class="ok">OK</span>');
if ($result['state'] !== 'ok') {
    echo '<div class="info-box" data-toggle="tooltip" title="' . esc_attr($result['message']) . '"></div>';
}
?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label>
                            <strong><?php _e('Php config', 'bng2woo-lite');?></strong>
                        </label>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="php_ini_check_row">
                            <span>allow_url_fopen :</span>
                            <?php if (ini_get('allow_url_fopen')): ?>
                                <span class="ok">On</span>
                            <?php else: ?>
                                <span class="error">Off</span><div class="info-box" data-toggle="tooltip" title="<?php _e('There may be problems with the image editor', 'bng2woo-lite');?>"></div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label>
                            <strong><?php _e('Internal AJAX call', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex("If you see Error here, then the background loading feature and the synchronization function don't work on your website. Need analyze php error log and server configutation to resolve the issue.", 'setting description', 'bng2woo-lite');?>"></div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="form-group input-block no-margin clearfix">
                            <?php
$result = B2WL_SystemInfo::ping();
echo ($result['state'] !== 'ok' ? '<span class="error">ERROR</span>' : '<span class="ok">OK</span>');
if (!empty($result['message'])) {
    echo '<div class="info-box" data-toggle="tooltip" title="' . esc_attr($result['message']) . '"></div>';
}
?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label>
                            <strong><?php _e('DISABLE_WP_CRON', 'bng2woo-lite');?></strong>
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="form-group input-block no-margin clearfix">
                            <?php echo (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) ? "Yes" : "No"; ?>
                            <div class="info-box" data-toggle="tooltip" title="<?php _ex('We recommend to disable WP Cron and setup the cron on your server/hosting instead.', 'setting description', 'bng2woo-lite');?>"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label>
                            <strong><?php _e('PHP DOM', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _ex('is there a DOM library', 'setting description', 'bng2woo-lite');?>"></div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="form-group input-block no-margin clearfix">
                            <?php
$result = B2WL_SystemInfo::php_dom_check();
echo ($result['state'] !== 'ok' ? '<span class="error">ERROR</span>' : '<span class="ok">OK</span>');
if (!empty($result['message'])) {
    echo '<div class="info-box" data-toggle="tooltip" title="' . esc_attr($result['message']) . '"></div>';
}
?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-lg-2">
                        <label>
                            <strong><?php _e('Import queue', 'bng2woo-lite');?></strong>
                        </label>
                        <div class="info-box" data-toggle="tooltip" title="<?php _e('Import queue', 'bng2woo-lite');?>"></div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-10">
                        <div class="form-group input-block no-margin">
                            <?php
$import_process = new B2WL_ImportProcess();
$num_in_queue = $import_process->num_in_queue();
?>
                            <span><?php echo esc_html($num_in_queue); ?></span>
                            <?php if ($num_in_queue > 0): ?>
                            <a class="b2wl-run-cron-queue" href="#">Run</a> | <a class="b2wl-clean-import-queue" href="#">Clean</a>
                            <?php endif;?>
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

    </div>
</form>

<script>
    (function ($) {
        $(function () {
            $('.b2wl-clean-log').click(function () {
                $.post(ajaxurl, {action: 'b2wl_clear_log_file'}).done(function (response) {
                    let json = $.parseJSON(response);
                    if (json.state !== 'ok') { console.log(json); }
                }).fail(function (xhr, status, error) {
                    console.log(error);
                });
                return false;
            });

            $('.b2wl-run-cron-queue').click(function () {
                if(confirm('Are you sure?')){
                    $.post(ajaxurl, {action: 'b2wl_run_cron_import_queue'}).done(function (response) {
                        let json = $.parseJSON(response);
                        if (json.state !== 'ok') { console.log(json); }
                    }).fail(function (xhr, status, error) {
                        console.log(error);
                    });
                }

                return false;
            });

            $('.b2wl-clean-import-queue').click(function () {
                if(confirm('Are you sure?')){
                    $.post(ajaxurl, {action: 'b2wl_clean_import_queue'}).done(function (response) {
                        let json = $.parseJSON(response);
                        if (json.state !== 'ok') { console.log(json); }
                    }).fail(function (xhr, status, error) {
                        console.log(error);
                    });
                }

                return false;
            });

        });
    })(jQuery);
</script>




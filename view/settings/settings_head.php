<h1><?php _ex('Banggood Dropship Settings', 'Setting title', 'bng2woo-lite');?></h1>
<div class="b2wl-content">
<?php include_once B2WL()->plugin_path() . '/view/chrome_notify.php';?>
    <div class="_b2wfo b2wl-info"><div>You are using Lite version of the plugin. If you want to unlock all features and get premium support, purchase the full version of the plugin.</div><a href="https://ali2woo.com/dropshipping-plugin-banggood/" target="_blank" class="btn">GET FULL VERSOIN</a></div>
    <ul class="nav nav-tabs">
      <?php foreach ($modules as $module): ?>
      <li role="presentation" <?php echo $current_module == $module['id'] ? 'class="active"' : ""; ?>><a href="<?php echo esc_url(admin_url('admin.php?page=b2wl_setting&subpage=' . $module['id'])); ?>"><?php echo esc_html($module['name']); ?></a></li>
      <?php endforeach;?>
    </ul>

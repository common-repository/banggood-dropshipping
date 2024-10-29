<div class="modal-overlay set-category-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('Link to category', 'bng2woo-lite'); ?></h3>
            <a class="modal-btn-close" href="#"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></a>
        </div>
        <div class="modal-body">
            <div>
                <label><?php _e('Categories'); ?>:</label>
                <?php $remember_categories = b2wl_get_setting('remember_categories', array()); ?>
                <select class="form-control select2 categories" data-placeholder="<?php _e('Choose Categories', 'bng2woo-lite'); ?>" multiple="multiple">
                    <option></option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo esc_attr($c['term_id']); ?>"<?php if (in_array($c['term_id'], $remember_categories)): ?> selected="selected"<?php endif; ?>><?php echo esc_html(str_repeat('- ', $c['level'] - 1) . $c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default no-btn" type="button"><?php _e('Cancel'); ?></button>
            <button class="btn btn-success yes-btn" type="button"><?php _e('Ok'); ?></button>
        </div>
    </div>
</div>
<script>
    (function ($) {
        $(".set-category-dialog .select2").select2({width: '100%'});
    })(jQuery);
</script>

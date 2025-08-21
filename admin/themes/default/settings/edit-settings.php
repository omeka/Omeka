<?php
queue_js_file('settings');
echo head(['title' => __('Settings'),'bodyclass' => 'settings edit-settings']);
echo common('settings-nav');
echo flash();
?>

<form method="post">
    <section class="seven columns alpha">
        <?php echo $this->form; ?>
        <?php fire_plugin_hook('admin_settings_form', ['form' => $form, 'view' => $this]); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Save Changes'), ['class'=>'submit full-width green button']); ?>
        </div>
    </section>
</form>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Omeka.Settings.checkImageMagick(
            <?php echo js_escape(url(["controller" => "settings", "action" => "check-imagemagick"])); ?>,
            <?php echo js_escape(__('Test')); ?>
        );
    });
</script>
<?php echo foot(); ?>

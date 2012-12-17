<?php
queue_js_file('security');
echo head(array('title' => __('Settings'), 'bodyclass' => 'settings edit-security'));
echo common('settings-nav');
echo flash();
?>

<form method="post">
    <section class="seven columns alpha">
        <?php echo $this->form; ?>
        <?php fire_plugin_hook('admin_settings_security_form', array('form' => $form, 'view' => $this)); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Save Changes'), array('class'=>'submit big green button')); ?>
        </div>
    </section>
</form>
<script type="text/javascript">
jQuery(document).ready(function () {
    Omeka.Security.buildRestoreButton('#file_extension_whitelist',
        <?php echo js_escape(url(array('controller'=>'settings','action'=>'get-file-extension-whitelist'))); ?>,
        <?php echo js_escape(__('Restore Default Extensions')); ?>
    );
    
    Omeka.Security.buildRestoreButton('#file_mime_type_whitelist',
        <?php echo js_escape(url(array('controller'=>'settings','action'=>'get-file-mime-type-whitelist'))); ?>,
        <?php echo js_escape(__('Restore Default Types')); ?>
    );
                      
    Omeka.Security.buildRestoreButton('#html_purifier_allowed_html_elements', 
        <?php echo js_escape(url(array('controller'=>'settings','action'=>'get-html-purifier-allowed-html-elements'))); ?>,
        <?php echo js_escape(__('Restore Default Elements')); ?>
    );

    Omeka.Security.buildRestoreButton('#html_purifier_allowed_html_attributes', 
        <?php echo js_escape(url(array('controller'=>'settings','action'=>'get-html-purifier-allowed-html-attributes'))); ?>,
        <?php echo js_escape(__('Restore Default Attributes')); ?>
    );
});
</script>
<?php echo foot(); ?>

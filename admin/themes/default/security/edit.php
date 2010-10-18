<?php head(array('title'=>'Edit Security Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('security'); ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    Omeka.Security.buildRestoreButton('#file_extension_whitelist',
        <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-extension-whitelist'))); ?>,
        'Restore Default File Extensions');
    
    Omeka.Security.buildRestoreButton('#file_mime_type_whitelist',
        <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-mime-type-whitelist'))); ?>,
        'Restore Default File Mime Types');
                      
    buildRestoreButton($('html_purifier_allowed_html_elements'), 
                    <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-html-purifier-allowed-html-elements'))); ?>,
                    'Restore Default Allowed Html Elements');

    buildRestoreButton($('html_purifier_allowed_html_attributes'), 
                    <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-html-purifier-allowed-html-attributes'))); ?>,
                    'Restore Default Allowed Html Attributes');
});
//]]>
</script>
<h1>Edit Security Settings</h1>
<?php common('settings-nav'); ?>

<div id="primary">
<?php echo flash(); ?>
<?php echo $this->form; ?>

</div>
<?php foot(); ?>

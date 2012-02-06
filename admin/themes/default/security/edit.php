<?php 
$pageTitle = __('Edit Security Settings');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('security'); ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    Omeka.Security.buildRestoreButton('#file_extension_whitelist',
        <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-extension-whitelist'))); ?>,
        <?php echo js_escape(__('Restore Default File Extensions')); ?>
    );
    
    Omeka.Security.buildRestoreButton('#file_mime_type_whitelist',
        <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-mime-type-whitelist'))); ?>,
        <?php echo js_escape(__('Restore Default File Mime Types')); ?>
    );
                      
    Omeka.Security.buildRestoreButton('#html_purifier_allowed_html_elements', 
        <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-html-purifier-allowed-html-elements'))); ?>,
        <?php echo js_escape(__('Restore Default Allowed Html Elements'_)); ?>
    );

    Omeka.Security.buildRestoreButton('#html_purifier_allowed_html_attributes', 
        <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-html-purifier-allowed-html-attributes'))); ?>,
        <?php echo js_escape(__('Restore Default Allowed Html Attributes')); ?>
    );
});
//]]>
</script>
<h1><?php echo $pageTitle; ?></h1>
<?php common('settings-nav'); ?>

<div id="primary">
<?php echo flash(); ?>
<?php echo $this->form; ?>

</div>
<?php foot(); ?>

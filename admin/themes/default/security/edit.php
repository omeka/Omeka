<?php 
$pageTitle = __('Edit Security Settings');
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js_tag('security'); ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    Omeka.Security.buildRestoreButton('#file_extension_whitelist',
        <?php echo js_escape(url(array('controller'=>'security','action'=>'get-file-extension-whitelist'))); ?>,
        <?php echo js_escape(__('Restore Default File Extensions')); ?>
    );
    
    Omeka.Security.buildRestoreButton('#file_mime_type_whitelist',
        <?php echo js_escape(url(array('controller'=>'security','action'=>'get-file-mime-type-whitelist'))); ?>,
        <?php echo js_escape(__('Restore Default File Mime Types')); ?>
    );
                      
    Omeka.Security.buildRestoreButton('#html_purifier_allowed_html_elements', 
        <?php echo js_escape(url(array('controller'=>'security','action'=>'get-html-purifier-allowed-html-elements'))); ?>,
        <?php echo js_escape(__('Restore Default Allowed Html Elements')); ?>
    );

    Omeka.Security.buildRestoreButton('#html_purifier_allowed_html_attributes', 
        <?php echo js_escape(url(array('controller'=>'security','action'=>'get-html-purifier-allowed-html-attributes'))); ?>,
        <?php echo js_escape(__('Restore Default Allowed Html Attributes')); ?>
    );
});
//]]>
</script>

<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>

<form method="post">

    <section class="seven columns alpha">
        <?php echo $this->form; ?>
    </section>

    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Save Changes'), array('class'=>'submit big green button')); ?>
        </div>
    </section>

</form>

<?php echo foot(); ?>

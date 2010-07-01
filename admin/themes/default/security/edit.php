<?php head(array('title'=>'Edit Security Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('security'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
Event.observe(window, 'load', function(){
    // var loaderGif = new Element('img', {'src': <?php echo js_escape(img("loader.gif")); ?>});
    
    buildRestoreButton($('file_extension_whitelist'), 
                         <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-extension-whitelist'))); ?>,
                         'Restore Default File Extensions');
    
    buildRestoreButton($('file_mime_type_whitelist'), 
                      <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-mime-type-whitelist'))); ?>,
                      'Restore Default File Mime Types');
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
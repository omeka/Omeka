<?php head(array('title'=>'Edit General Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('settings'); ?>
<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function () {
        Omeka.Settings.checkImageMagick(<?php echo js_escape(uri(array("controller"=>"settings","action"=>"check-imagemagick"))); ?>);
    });
//]]>    
</script>

<h1>Edit General Settings</h1>

<?php common('settings-nav'); ?>

<div id="primary">
<?php echo flash(); ?>
<?php echo $this->form; ?>
</div>
<?php foot();

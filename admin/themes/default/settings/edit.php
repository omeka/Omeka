<?php
$pageTitle = __('Edit General Settings');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('settings'); ?>
<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function () {
        Omeka.Settings.checkImageMagick(
            <?php echo js_escape(url(array("controller" => "settings", "action" => "check-imagemagick"))); ?>,
            <?php echo js_escape(__('Test')); ?>
        );
    });
//]]>    
</script>

<div class="seven columns alpha">
<?php common('settings-nav'); ?>
<?php echo flash(); ?>
<?php echo $this->form; ?>
</div>

<?php foot(); ?>

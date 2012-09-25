<?php
$pageTitle = __('Edit General Settings');
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js_tag('settings'); ?>
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
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<?php echo $this->form; ?>
</div>

<?php echo foot(); ?>

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

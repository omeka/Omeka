<?php
queue_js_file(array('vendor/tiny_mce/tiny_mce', 'themes'));
echo head(array('title'=> __('Appearance'), 'bodyclass' => 'themes theme-configuration'));
echo common('appearance-nav');
echo flash();
?>

<form method="post" action="" enctype="multipart/form-data">
    <section class="seven columns alpha">
        <h2><?php echo __('Configure Theme: %s', html_escape($theme->title)); ?></h2>
        <p><?php echo __('Configurations apply to this theme only.'); ?></p>
        <?php echo $configForm; ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit', __('Save Changes'), array('class'=>'submit big green button')); ?>
        </div>
    </section>
</form>

<?php echo foot(); ?>

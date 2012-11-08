<?php
$pageTitle = __('Edit Appearance Settings');
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>

<?php echo common('appearance-nav'); ?>

<?php echo flash(); ?>

<form id="appearance-form" enctype="application/x-www-form-urlencoded" method="post">

<section class="seven columns alpha">
        
    <?php echo $this->form->getDisplayGroup('appearance'); ?>
    
    <?php fire_plugin_hook('admin_appearance_settings_form', array('appearance_settings_form' => $form, 'view' => $this)); ?>

</section>

<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('appearance_submit', __('Save Changes'), array('class'=>'submit big green button')); ?>
    </div>
</section>

</form>

<?php echo foot(); ?>

<?php
$pageTitle = __('Appearance');
$sectionNav = common('appearance-nav');
echo head(['title'=>$pageTitle, 'bodyclass'=>'settings', 'sectionNav'=>$sectionNav]); ?>


<?php echo flash(); ?>

<form id="appearance-form" method="post">

<section class="seven columns alpha">
        
    <?php echo $this->form; ?>
    
    <?php fire_plugin_hook('admin_appearance_settings_form', ['form' => $form, 'view' => $this]); ?>

</section>

<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('appearance_submit', __('Save Changes'), ['class'=>'submit full-width green button']); ?>
    </div>
</section>

</form>

<?php echo foot(); ?>

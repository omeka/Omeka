<?php
echo head(array('title' => __('Add New User'), 'bodyclass' => 'users'));
echo flash();
?>
<section class="seven columns alpha">
    <?php echo $this->form; ?>
    <?php fire_plugin_hook('admin_users_form', array('form' => $form, 'view' => $this)); ?>
</section>

<?php echo foot();?>

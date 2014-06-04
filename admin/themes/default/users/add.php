<?php
echo head(array('title' => __('Add New User'), 'bodyclass' => 'users'));
echo flash();
?>
<form method="post">
<section class="seven columns alpha">
    <p class='explanation'>* <?php echo __('required field'); ?></p>
    <?php echo $this->form; ?>
    <?php fire_plugin_hook('admin_users_form', array('form' => $form, 'view' => $this)); ?>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('submit', __('Add User'), array('class' => 'submit big green button')); ?>
        <?php fire_plugin_hook('admin_users_panel_buttons', array('user' => $user, 'view' => $this)); ?>
    </div>
</section>
</form>

<?php echo foot();?>

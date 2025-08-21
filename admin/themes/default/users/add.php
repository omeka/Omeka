<?php
echo head(['title' => __('Add New User'), 'bodyclass' => 'users']);
echo flash();
?>
<form method="post">
<section class="seven columns alpha">
    <?php echo $this->form; ?>
    <?php fire_plugin_hook('admin_users_form', ['form' => $form, 'view' => $this]); ?>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('submit', __('Add User'), ['class' => 'submit full-width green button']); ?>
        <?php fire_plugin_hook('admin_users_panel_buttons', ['user' => $user, 'view' => $this]); ?>
    </div>
</section>
</form>

<?php echo foot();?>

<?php
$userTitle = $user->username;
if ($userTitle != '') {
    $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
} else {
    $userTitle = '';
}
$userTitle = __('Edit User #%s', $user->id) . $userTitle;
echo head(['title' => $userTitle, 'bodyclass' => 'users']);
echo common('users-nav', ['user' => $user]);
echo flash();
?>
<form method="post">
<section class="seven columns alpha">
    <?php echo $this->form; ?>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <?php
        echo $this->formSubmit('submit', __('Save Changes'), ['class' => 'submit full-width green button']);
        if (!$user->active):
            echo $this->formSubmit('resend_activation_email', __('Resend Activation Email'), ['class' => 'submit full-width blue button']);
        endif;
        if (is_allowed($user, 'delete')):
            echo link_to($user, 'delete-confirm', __('Delete'), ['class' => 'full-width red button delete-confirm']);
        endif;
        ?>
        <?php fire_plugin_hook('admin_users_panel_buttons', ['record' => $user, 'view' => $this]); ?>
        <?php fire_plugin_hook('admin_users_panel_fields', ['record' => $user, 'view' => $this]); ?>
    </div>
</section>
</form>
<?php fire_plugin_hook('admin_users_form', ['user' => $user, 'form' => $form, 'view' => $this]); ?>

<?php echo foot();?>

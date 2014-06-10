<?php
$userTitle = strip_formatting($user->username);
if ($userTitle != '') {
    $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
} else {
    $userTitle = '';
}
$userTitle = __('Edit User #%s', $user->id) . $userTitle;
echo head(array('title' => $userTitle, 'bodyclass' => 'users'));
echo common('users-nav', array('user' => $user));
echo flash();
?>
<form method="post">
<section class="seven columns alpha">
    <p class='explanation'>* <?php echo __('required field'); ?></p>
    <?php echo $this->form; ?>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <?php
        echo $this->formSubmit('submit', __('Save Changes'), array('class' => 'submit big green button'));
        if (!$user->active):
            echo $this->formSubmit('resend_activation_email', __('Resend Activation Email'), array('class' => 'submit big blue button'));
        endif;
        if (is_allowed($user, 'delete')):
            echo link_to($user, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm'));
        endif;
        ?>
        <?php fire_plugin_hook('admin_users_panel_buttons', array('record' => $user, 'view' => $this)); ?>
        <?php fire_plugin_hook('admin_users_panel_fields', array('record' => $user, 'view' => $this)); ?>
    </div>
</section>
</form>
<?php fire_plugin_hook('admin_users_form', array('user' => $user, 'form' => $form, 'view' => $this)); ?>

<?php echo foot();?>

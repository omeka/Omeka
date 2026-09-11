<?php
$userTitle = $user->username;
if ($userTitle != '') {
    $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
} else {
    $userTitle = '';
}
$userTitle = __('Edit User #%s', $user->id) . $userTitle;
$sectionNav = common('users-nav', ['user' => $user]);
echo head(['title' => $userTitle, 'bodyclass' => 'users', 'sectionNav' => $sectionNav]);
echo flash();
?>
<form id="change-password" method="post">
<section class="seven columns alpha">
    <?php echo $this->form; ?>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('submit', __('Save Password'), ['class' => 'submit full-width green button']); ?>
    </div>
</section>
</form>

<?php echo foot();?>

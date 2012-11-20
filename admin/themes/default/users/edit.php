<?php
$userTitle = strip_formatting($user->username);
if ($userTitle != '') {
    $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
} else {
    $userTitle = '';
}
$userTitle = __('Edit User #%s', $user->id) . $userTitle;
echo head(array('title' => $userTitle, 'bodyclass' => 'users'));
?>

<?php echo flash(); ?>
<section class="seven columns alpha">
    <?php echo $this->form; ?>
    <?php if (is_allowed($user, 'delete')): ?>
    <?php echo link_to($user, 'delete-confirm', __('Delete'), array('class' => 'red button delete-confirm')); ?>
    <?php endif; ?>

    <?php echo $this->passwordForm; ?>
</section>

<?php echo foot();?>

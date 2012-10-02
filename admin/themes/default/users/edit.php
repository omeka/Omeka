<?php
    $userTitle = strip_formatting($user->username);
    if ($userTitle != '') {
        $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
    } else {
        $userTitle = '';
    }
    $userTitle = __('Edit User #%s', $user->id) . $userTitle;
?>
<?php echo head(array('title'=> $userTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>

<?php echo flash(); ?>
<div class="seven columns alpha">
<?php echo $this->form; ?>
<?php if (is_allowed($user, 'delete')): ?>
    <?php echo link_to($user, 'delete-confirm', __('Delete'), array('class'=>'red button delete-confirm')); ?>
<?php endif; ?>

<?php echo $this->passwordForm; ?>

</div>

<?php echo foot();?>

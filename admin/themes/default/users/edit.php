<?php
    $userTitle = strip_formatting($user->username);
    if ($userTitle != '') {
        $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
    } else {
        $userTitle = '';
    }
    $userTitle = __('Edit User #%s', $user->id) . $userTitle;
?>
<?php head(array('title'=> $userTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1><?php echo $userTitle; ?></h1>
<?php common('settings-nav'); ?>

<div id="primary">
<?php if (has_permission($user, 'delete')): ?>
    <?php echo delete_button(null, 'delete-user', 'Delete this User', array(), 'delete-record-form'); ?>
<?php endif; ?>
<?php echo $this->form; ?>
<?php echo $this->passwordForm; ?>
</div>
<?php foot();?>

<?php
    $userTitle = strip_formatting($user->username);
    if ($userTitle != '') {
        $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
    } else {
        $userTitle = '';
    }
    $userTitle = 'User #' . $user->id . $userTitle;
?>
<?php head(array('title'=>$userTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'themes primary'));?>
<h1><?php echo $userTitle; ?></h1>

<?php if (has_permission('Users', 'edit')): ?>
<p id="edit-item" class="edit-button"><?php 
echo link_to($user, 'edit', 'Edit this User', array('class'=>'edit')); ?></p>   
<?php endif; ?>

<?php common('settings-nav'); ?>

<div id="primary">

    <h2>Username</h2>
    <p><?php echo html_escape($user->username); ?></p>
    <h2>Real Name</h2>
    <p><?php echo html_escape($user->first_name . ' ' . $user->last_name); ?></p>
    <h2>Email</h2>
    <p><?php echo html_escape($user->email); ?></p>
    <h2>Institution</h2>
    <p><?php echo html_escape($user->institution); ?></p>
</div>
<?php foot();?>
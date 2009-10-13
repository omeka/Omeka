<?php
    $userTitle = strip_formatting($user->username);
    if ($userTitle != '') {
        $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
    } else {
        $userTitle = '';
    }
    $userTitle = 'Edit User #' . $user->id . $userTitle;
?>
<?php head(array('title'=> $userTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1><?php echo $userTitle; ?></h1>
<?php common('settings-nav'); ?>

<div id="primary">
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="Save Changes" class="submit submit-medium" />
</form>

<form action="<?php echo html_escape(uri('users/change-password/', array('id'=>$user->id))); ?>" method="post" accept-charset="utf-8">

<fieldset>
    <legend>Change Password</legend>
    
    <div class="field">
        <?php echo label('new_password1','New Password'); ?>
        <div class="inputs">
        <input type="password" name="new_password1" id="new_password1" class="textinput" />
        </div>
    </div>

    <div class="field">
        <?php echo label('new_password2','Repeat New Password'); ?>
        <div class="inputs">    
        <input type="password" name="new_password2" id="new_password2" class="textinput" />
        </div>
    </div>
    
    <?php if (has_permission('super')): ?>
    <input type="hidden" name="old_password" id="old_password" />
    <?php else: ?>
    <div class="field">
        <?php echo label('old_password','Current Password'); ?>
        <div class="inputs">
        <input type="password" name="old_password" id="old_password" class="textinput"/>
        </div>
    </div>
    <?php endif; ?>
</fieldset>

    <input type="submit" class="submit submit-medium" name="submit" value="Save Password"  class="button" />
</form>
</div>
<?php foot();?>
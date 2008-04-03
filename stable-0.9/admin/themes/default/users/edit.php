<?php head();?>
<?php common('users-nav'); ?>

<div id="primary">
<h1>Edit a User</h1>
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="Save User Information" class="button" />
</form>

<form action="<?php echo uri('users/changePassword/', array('id'=>$user->id)); ?>" method="post" accept-charset="utf-8">

<fieldset>
	<legend>Change Password</legend>
	
	<div class="field">
		<label for="new_password1">New Password</label>	
		<input type="password" name="new_password1" id="new_password1" class="textinput" />
	</div>

	<div class="field">
		<label for="new_password2">Repeat New Password</label>	
		<input type="password" name="new_password2" id="new_password2" class="textinput" />
	</div>
	
	<div class="field">
		<label for="old_password">Current password</label>
		<input type="password" name="old_password" id="old_password" <?php if(has_permission('super')): ?>disabled="disabled" class="textinput disabled"<?php else: ?>class="textinput"<?php endif; ?>/>
	</div>
</fieldset>

	<input type="submit" name="submit" value="Change Password"  class="button" />
</form>
</div>
<?php foot();?>
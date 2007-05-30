<?php head();?>
<h2>Users | Edit a User</h2>
<form method="post">
<?php include('form.php'); ?>
<?php/*
<fieldset>
<legend>Change this user's password</legend>
<div class="field">
	<?php password(array('name'=>'old_password', 'class'=>'textinput', 'id'=>'old_password'),'Old Password'); ?>
</div>
<div class="field">
	<?php password(array('name'=>'new_password1', 'class'=>'textinput', 'id'=>'new_password1'),'New Password'); ?>
</div>

<div class="field">
	<?php password(array('name'=>'new_password2', 'class'=>'textinput', 'id'=>'new_password2'), 'Retype Password'); ?>
</div>
*/ ?>
<fieldset>
	<legend>Change Password</legend>
	
	<div class="field">
		<label for="old_password">Current password</label>
		<input type="password" name="old_password" id="old_password" class="textinput" />
	</div>

	<div class="field">
		<label for="new_password1">New Password</label>	
		<input type="password" name="new_password1" id="new_password1" class="textinput" />
	</div>

	<div class="field">
		<label for="new_password2">Repeat New Password</label>	
		<input type="password" name="new_password2" id="new_password2" class="textinput" />
	</div>
</fieldset>

<input type="submit" name="submit" value="Edit" />
</form>
<?php foot();?>
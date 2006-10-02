<?php
//Layout: default;
$e = $__c->accounts()->edit();
?>
<?php include( 'subnav.php' ); ?>
<h2>My Profile</h2>
<div id="my-account-edit">
<?php if ($e): ?>
<div class="alert"><p>Your password has been changed!</p></div>

<?php endif; ?>

<form method="post" action="<?php echo $_link->to( 'account', 'edit' ); ?>">
	<label for="old_password">Old Password:</label>
	<input type="password" name="old_password" id="old_password" /><br/>
	<label for="new_password_1">New Password:</label>
	<input type="password" name="new_password_1" id="new_password_1" /><br/>
	<label for="new_password_2">Repeat New Password:</label>
	<input type="password" name="new_password_2" id="new_password_2" /><br/>
	<input type="submit" value="Change Password" name="change_password"/>
</form>
</div>

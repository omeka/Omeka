<?php head(array(), 'login-header'); ?>
<h1>Forgot Password</h1>
<?php echo flash(); ?>
<form method="post" accept-charset="utf-8">
	<label for="email">Please provide your email address:</label>
	<input type="text" name="email" id="email" class="textinput" value="<?php echo @$_POST['email']; ?>" />
	<input type="submit" class="submitinput" value="Submit" />
</form>
<p>Back to <a href="<?php echo uri('users/login'); ?>">login</a>.</p>
<?php foot(array(), 'login-footer'); ?>
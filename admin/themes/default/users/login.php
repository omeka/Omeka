<?php head(array(), 'login'); ?>
<div id="login">
<h2>Login</h2>
	<?php
	if (isset($errorMessage)) {
		echo '<div class="error">Error: <span>'.$errorMessage.'</span></div>';
	}
	?>
<form action="<?php echo uri('users/login');?>" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="field">
	<label for="username">Username</label> 
	<input type="text" name="username" class="textinput" id="username" />
	</div>
	<div class="field">
	<label for="password">Password</label> 
	<input type="password" name="password" class="textinput" id="password" />
	</div>
	<input type="submit" value="Login" />
	</fieldset>
</form>

</div>
<div id="forgotpassword"><a href="#">Lost your password?</a></div>
<?php foot(); ?>
<?php head(array(), 'login'); ?>

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

<ul>
<li id="backtosite"><a href="<?php echo uri(''); ?>">Back to <?php settings('site_title'); ?></a></li>
<li id="forgotpassword"><a href="<?php echo uri('users/forgotPassword'); ?>">Lost your password?</a></li>
</ul>

<?php foot(); ?>
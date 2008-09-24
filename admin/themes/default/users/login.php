<?php head(array('bodyclass'=>'login'), 'login-header'); ?>

<h1>Login</h1>
	<?php
	if (isset($errorMessage)):
		?><div class="error">Error: <span>
			
		<?php echo htmlentities($errorMessage); ?>
		</span></div>
	<?php endif; ?>
	
<form id="login-form" action="<?php echo uri('users/login');?>" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="field">
	<label for="username">Username</label> 
	<input type="text" name="username" class="textinput" id="username" />
	</div>
	<div class="field">
	<label for="password">Password</label> 
	<input type="password" name="password" class="textinput" id="password" />
	</div>
	</fieldset>
	<input type="submit" class="login" value="Login" />
</form>

<p id="login-links">
<span id="backtosite"><?php echo link_to_home_page('View Public Site'); ?></span>  |  <span id="forgotpassword"><a href="<?php echo uri('users/forgot-password'); ?>">Lost your password?</a></spans>
</p>

<?php foot(array(),'login-footer'); ?>
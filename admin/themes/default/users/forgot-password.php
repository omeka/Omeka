<?php head(array('bodyclass'=>'login'), 'login-header'); ?>
<h1>Forgot Password</h1>
<p id="login-links">
<span id="backtologin"><a href="<?php echo uri('users/login'); ?>">Back to Log In</a></spans>
</p>

<p class="clear">Enter your email address to retrieve your password.</p>
<?php echo flash(); ?>
<form method="post" accept-charset="utf-8">
    <div class="field">        
    	<label for="email">Email</label>
    	<input type="text" name="email" id="email" class="textinput" value="<?php echo @$_POST['email']; ?>" />
	</div>

	<input type="submit" class="submit submit-small" value="Submit" />
</form>
<?php foot(array(), 'login-footer'); ?>
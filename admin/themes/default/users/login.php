<?php head(array(), 'login'); ?>

<h1>Login</h1>
	<?php
	if (isset($errorMessage)):
		?><div class="error">Error: <span>
			
		<?php 
			foreach ($errorMessage as $msg): ?>
			<?php echo $msg; ?>
		<?php endforeach; ?>
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

<ul>
<li id="backtosite"><?php link_to_home_page('View Public Site', array('id'=>'public-link')); ?></li>
<li id="forgotpassword"><a href="<?php echo uri('users/forgotPassword'); ?>">Lost your password?</a></li>
</ul>

<?php foot(); ?>
<?php head(); ?>

<?php echo $user->username; ?>

<form method="post">
	<fieldset>
	<div class="field">
	<label for="new_password1">Create a Password</label>
	<input type="password" name="new_password1" id="new_password1" />
	</div>
	<div class="field">
	<label for="new_password2">Create a Password</label>
	<input type="password" name="new_password2" id="new_password2" />
	</div>
	</fieldset>
	
	<input type="submit" name="submit" value="Activate your account"/>
	
</form>
<?php foot(); ?>

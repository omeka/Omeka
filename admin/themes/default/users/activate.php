<?php head(); ?>

<?php echo $user->username; ?>

<form method="post">
	<input type="password" name="new_password1" id="new_password1"/>
	<input type="password" name="new_password2" id="new_password2"/>
	<input type="submit" name="submit" value="Activate your account"/>
</form>
<?php foot(); ?>

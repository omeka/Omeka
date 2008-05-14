<?php head(array(), 'login'); ?>
<h1>Forgot Password</h1>
<?php echo flash(); ?>
<form method="post" accept-charset="utf-8">
	<label for="email">Please provide your email address:</label>
	<input type="text" name="email" id="email" class="textinput" value="<?php echo @$_POST['email']; ?>" />
	<input type="submit" value="Submit" />
</form>

<?php foot(); ?>
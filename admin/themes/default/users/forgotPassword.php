<?php head(); ?>
<div id="primary">
<?php echo flash(); ?>
<form method="post" accept-charset="utf-8">
	<label for="email">Please provide your email address:</label>
	<input type="text" name="email" id="email" value="<?php echo @$_POST['email']; ?>" />
	<input type="submit" value="Submit" />
</form>
</div>
<?php foot(); ?>
<?php head(); ?>
<h2>Edit Type: <?php echo $type->name; ?></h2>
<form method="post">
	<?php include 'form.php';?>
	<input type="submit" name="submit" value="Submit" />
</form>
<?php foot(); ?>
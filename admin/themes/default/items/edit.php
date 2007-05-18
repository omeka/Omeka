<?php head();?>
<?php common('archive-nav'); ?>
<h2>Edit &#8220;<?php echo $item->title; ?>&#8221;</h2>
<form method="post" enctype="multipart/form-data">
	<?php include 'form.php'; ?>
	<fieldset>
	<input type="submit" name="submit" value="Save Item" />
	</fieldset>
</form>
<?php foot();?>

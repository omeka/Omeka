<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
	
<h2><?php echo h($collection->name); ?></h2>

<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit" />
</form>

</div>
<?php foot(); ?>
<?php head(); ?>
<?php common('archive-nav'); ?>
<h2>Edit Collection: <?php echo $collection->name; ?></h2>
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit" />
</form>
<?php foot(); ?>
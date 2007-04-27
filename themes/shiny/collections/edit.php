<?php head(); ?>
<h2>Edit Collection: <?php echo $collection->name; ?></h2>
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit this form --&gt;" />
</form>
<?php foot(); ?>
<?php head(); ?>
<?php common('exhibits-nav'); ?>
<div id="primary">
<h2>Edit Exhibit: <?php echo $exhibit->title; ?></h2>
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit" />
</form>
</div>
<?php foot(); ?>
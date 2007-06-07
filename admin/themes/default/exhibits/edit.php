<?php head(); ?>
<ul id="tertiary-nav" class="navigation">
	<?php 
		nav(array('Browse Exhibits' => uri('exhibits/browse'), 'Add Exhibit' => uri('exhibits/add')));
	?>
</ul>
<h2>Edit Exhibit: <?php echo $exhibit->title; ?></h2>
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" value="Submit" />
</form>
<?php foot(); ?>
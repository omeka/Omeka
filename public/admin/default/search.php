<?php
    head(); 
	echo "There are $total records stored in the search index.  ";

?>
<?php echo count($results);?> were returned.

<form method="post">
	<input type="text" name="search" value="<?php echo $_POST['search'];?>" />
	<input type="submit" name="submit" value="Search" />
</form>

<?php foreach( $results as $key => $result ): ?>

	<?php  	if($result instanceof Item): ?>
				<?php echo $result->title; ?>
				<!-- Display the Item in a special way -->
	<?php 	elseif($result instanceof Type): ?>
				<?php echo $type->name; ?>
	<?php 	endif;?>
			
<?php endforeach; ?>
<?php foot(); ?>
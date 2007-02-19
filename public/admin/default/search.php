
<?php echo count($results);?> were returned.

<form method="post">
	<input type="text" name="search" value="<?php echo $_POST['search'];?>" />
	<input type="submit" name="submit" value="Search" />
</form>

<?php foreach( $results as $key => $result ): ?>
	<?php

		echo get_class($result);
		$result->dump();

	?>
	<?php  	if($result instanceof Item): ?>
				<!-- Display the Item in a special way -->
	<?php 	elseif($result instanceof Type): ?>
				<?php echo $type->name; ?>
	<?php 	endif;?>
			
<?php endforeach; ?>
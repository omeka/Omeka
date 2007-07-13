<?php head(); ?>

	<h1>Show Entity</h1>
	
	
	<?php foreach( $entity as $key => $value ): ?>
		<dl><dt><?php echo $key; ?></dt><dd><?php echo $value; ?></dd></dl>
	<?php endforeach; ?>


<?php foot(); ?>
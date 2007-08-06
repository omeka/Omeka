<?php head(); ?>

	<?php echo h($exhibit->title); ?>
	
	<?php echo h($section->title); ?>
		
	<?php echo flash(); ?>
	<?php 
		render_exhibit_page();
	?>
		
<?php foot(); ?>
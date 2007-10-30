<?php head(array('title'=>'Exhibit #'.$exhibit->id, 'body_class'=>'exhibits')); ?>

	<?php echo h($exhibit->title); ?>
	
	<?php echo h($section->title); ?>
		
	<?php echo flash(); ?>
	<?php 
		render_exhibit_page();
	?>
		
<?php foot(); ?>
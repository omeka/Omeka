<?php head(); ?>

	<?php echo $exhibit->title; ?>
	<?php section_nav();?>
	
	<?php echo $section->title; ?>
		<?php page_nav();?>
		
	<?php echo flash(); ?>
	<?php render_exhibit_page(); ?>
		
<?php foot(); ?>
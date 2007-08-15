<?php head(); ?>

	<h2><?php echo $exhibit->title; ?></h2>
	<?php section_nav();?>
	
	<h3><?php echo $section->title; ?></h3>
		<?php page_nav();?>
		
	<?php echo flash(); ?>
	<?php render_exhibit_page(); ?>
		
<?php foot(); ?>
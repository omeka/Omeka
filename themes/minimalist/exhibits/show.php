<?php head(); ?>

	<h1><?php echo $exhibit->title; ?></h1>
	<?php section_nav();?>
	
	<h2><?php echo $section->title; ?></h2>
		<?php page_nav();?>
		
	<?php echo flash(); ?>
	<?php render_exhibit_page(); ?>
		
<?php foot(); ?>
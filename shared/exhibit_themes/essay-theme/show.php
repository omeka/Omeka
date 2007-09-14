<?php head(); ?>

	<div id="primary">
	<?php section_nav();?>
	</div>
	
	<div id="secondary">
	<h2><?php echo $exhibit->title; ?></h2>
	<h3><?php echo $section->title; ?></h3>
	
	<?php echo flash(); ?>
	<?php render_exhibit_page(); ?>

<?php foot(); ?>
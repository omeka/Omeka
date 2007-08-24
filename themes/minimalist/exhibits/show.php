<?php head(); ?>

	<h2><?php echo $exhibit->title; ?></h2>

<div id="nav-container">
	<?php section_nav();?>
	<?php page_nav();?>
</div>

	<?php echo flash(); ?>
	<?php render_exhibit_page(); ?>
		
<?php foot(); ?>
<?php head(); ?>

	<h2><?php echo $exhibit->title; ?></h2>
	<?php section_nav();?>
	
	<h3><?php echo $section->title; ?></h3>
		<?php page_nav();?>
		
	<?php echo flash(); ?>
	<?php render_exhibit_page(); ?>
		<h3>Description</h3>
<?php echo nls2p($exhibit->description); ?>

<h3>Credits</h3>
<p><?php echo h($exhibit->credits); ?></p>
<?php foot(); ?>
<?php exhibit_head(); ?>

	<div id="primary">
	<?php section_nav();?>
	</div>
	
	<div id="secondary">
		<div id="module-nav">
			<h4>Modules</h4>
			<ul>
				<li><a href="#">1</a></li>
				<li><a href="#">2</a></li>
				<li><a href="#">3</a></li>
				<li><a href="#">4</a></li>
				<li><a href="#">5</a></li>
				<li><a href="#">6</a></li>
			</ul>
		</div><!--end module nav-->
		
	<h2><?php echo $exhibit->title; ?></h2>
	<h3><?php echo $section->title; ?></h3>
	
	<?php echo flash(); ?>
	<?php render_exhibit_page(); ?>

<?php foot(); ?>
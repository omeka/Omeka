<?php exhibit_head(); ?>
<h2><?php echo h($section->title); ?></h2>			
			
<div id="primary">
	<div class="exhibit-content"><!--exhibit content-->
	<?php render_exhibit_page(); ?>
	</div>
</div><!--end primary-->
	
<?php exhibit_foot(); ?>
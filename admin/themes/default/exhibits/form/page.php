<?php head(); ?>
<style type="text/css" media="screen">
@import url('<?php layout_css(); ?>');
#item-select {float:left; width: 378px;}
#layout-all {float:right; width:378px;}

</style>

<script type="text/javascript" charset="utf-8">
	Event.observe(window, 'load', function() {
		dragDropPage();
	});
</script>
<?php js('exhibits'); ?>
<?php common('exhibits-nav'); ?>
<div id="exhibit-page">
<?php if ( empty($page->layout) ): ?>

<h2>Choose a layout for the page</h2>
<form method="post" id="choose-layout">
	<div id="layouts">
<?php 
	$layouts = get_ex_layouts();
	
	foreach ($layouts as $layout) {
		exhibit_layout($layout);
	} 
?>
	</div> 
	<?php submit('Choose this layout','choose_layout');?>
</form>

<?php else: ?>


	<?php 
		if(!$page->exists()) {
			$url = uri('exhibits/addPage').DIRECTORY_SEPARATOR.$section->id.DIRECTORY_SEPARATOR; 
		}else {
			$url = uri('exhibits/editPage').DIRECTORY_SEPARATOR.$page->id.DIRECTORY_SEPARATOR;
		}
		
	?>
	
	
	<?php 
	//Retrieve items with their pagination
	$retVal = _make_omeka_request('Items','browse',array('pagination_url'=>$url, 'public'=>true),array('items','pagination'));
	extract($retVal);
	?>
		
	<div id="item-select">
		<div id="pagination">
		<?php 
			 echo pagination_links(); 
		?>
		<p class="warning">(Warning: You must save the form before paginating through the items otherwise its contents may be erased)</p>
		</div>
		
		<?php foreach( $items as $k => $item ): ?>
			<div class="item-drop">
				<div class="item-drag">
					<div class="item_id"><?php echo h($item->id); ?></div>
					<?php 
						if(has_thumbnail($item)){
							thumbnail($item);
						} else {
							echo h($item->title);
						}
					?>
				</div>
				<div class="item_id"><?php echo h($item->id); ?></div>
			</div>
		<?php endforeach; ?>
		<h2 id="search-header" class="close">Search Items</h2>
		
		<?php items_filter_form(array('id'=>'search'), $url); ?>
			
	</div>
<form name="layout" id="layout-all" method="post">
	<div id="layout-form">
	<?php render_layout_form($page->layout); ?>
	</div>
<?php
	submit('Save Changes &amp; Continue Paginating Through Items', 'save_and_paginate'); 
	submit('Save &amp; Return to Exhibit', 'exhibit_form');
	submit('Save &amp; Return to Section', 'section_form');
	submit('Save &amp; Add Another Page', 'page_form');
	submit('Change the layout for this page', 'change_layout');	
?>
</form>
	<?php if ( $page->exists() ): ?>
		<form action="<?php echo uri('exhibits/deletePage/'.$page->id); ?>">
		<?php submit('Cancel/Delete this page', 'delete_page'); ?>
		</form>
	<?php else: ?>
		<form method="get">
			<?php submit('Cancel Adding This Page', 'cancel'); ?>
		</form>
	<?php endif; ?>
<?php endif; ?>
</div>
<?php foot(); ?>
<?php head(); ?>
<style type="text/css" charset="utf-8">
	.item-drop {
		width: 150px;
		height: 150px;
		border: 1px solid black;
		display:block;
	}
	
	#item-select {
		float:right;
		display:inline;
		height: 200px;
	}
	#item-select .item {
		display:block;
	}
	
	#layout-form {
		clear:both;	
	}
</style>

<script type="text/javascript" charset="utf-8">
	Event.observe(window, 'load', function() {
		dragDropPage();
	});
</script>
<?php js('exhibits'); ?>
<?php common('exhibits-nav'); ?>
<div id="primary">
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
		items_filter_form(array(), $url);
	?>
		
	<div id="item-select">
		<div id="pagination">
		<?php 
			 echo pagination(); 
		?>
		<p class="warning">(Warning: You must save the form before paginating through the items otherwise its contents may be erased)</p>
		</div>
		
		<?php foreach( $items as $k => $item ): ?>
			<div class="item-drop">
				<div class="item-drag">
					<div class="item_id"><?php echo $item->id; ?></div>
			
					<?php 
						if(has_thumbnail($item)){
							thumbnail($item);
						} else {
							echo $item->title;
						}
					?>
				</div>
				<div class="item_id"><?php echo $item->id; ?></div>
			</div>
		<?php endforeach; ?>	
	</div>
<form name="layout" id="layout-all" method="post">
	

	<div id="layout-form">
<?php 
	render_layout_form($page->layout);
?>
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
		<?php 
			submit('Cancel/Delete this page', 'delete_page'); 
		?>
	
	</form>
	<?php else: ?>
		<form method="get">
			<input type="submit" name="cancel" value="Cancel adding this page" />
		</form>
	<?php endif; ?>

<?php endif; ?>
</div>
<?php foot(); ?>
<?php head(); ?>

<?php if ( empty($page->layout) ): ?>

<h2>Choose a layout for the page</h2>
<form method="post">
<?php 
	$layouts = get_ex_layouts();
	
	foreach ($layouts as $layout) {
		exhibit_layout($layout);
	} 
	radio(array('name'=>'layout'), array_combine($layouts,$layouts), $page->layout);
	submit('Choose this layout','choose_layout');
?>
</form>

<?php else: ?>

<form method="post">
	<?php 
		if(!$page->exists()) {
			$url = uri('exhibits/addPage').DIRECTORY_SEPARATOR.$section->id.DIRECTORY_SEPARATOR; 
		}else {
			$url = uri('exhibits/editPage').DIRECTORY_SEPARATOR.$page->id.DIRECTORY_SEPARATOR;
		}
		
	?>
	
	<div id="item-select">
	<?php 
	//Retrieve items with their pagination
	$retVal = _make_omeka_request('Items','browse',array('pagination_url'=>$url),array('items','pagination'));
	extract($retVal);
	
	?>
	
	<?php foreach( $items as $item ): ?>
		<div class="item">
			<div class="item_id">
				<?php echo $item->id; ?>
			</div>
			
			<?php 
				if(has_thumbnail($item)){
					thumbnail($item);
				} else {
					echo $item->title;
				}
			?>
		</div>
	<?php endforeach; ?>
<?php 
	 echo $pagination; 
?>
	</div>
	
	<p>(Warning: You must save the form before paginating through the items otherwise its contents may be erased)</p>

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
?>
</form>

<?php endif; ?>

<?php foot(); ?>
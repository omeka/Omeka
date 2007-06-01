<?php head(); ?>

<?php if ( empty($page->layout) ): ?>

<h2>Choose a layout for the page</h2>
<form method="post">
<?php 
	$layouts = get_ex_layouts();
	
	foreach ($layouts as $layout) {
		ex_layout($layout);
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
	
	<?php show_items_and_pagination($url); ?>
	<p>(Warning: You must save the form before paginating through the items otherwise its contents may be erased)</p>
	
<?php 
	render_layout_form($page->layout);
	
	submit('Save Changes &amp; Continue Paginating Through Items', 'save_and_paginate'); 
	submit('Save &amp; Return to Exhibit', 'exhibit_form');
	submit('Save &amp; Return to Section', 'section_form');
	submit('Save &amp; Add Another Page', 'page_form');
?>
</form>

<?php endif; ?>

<?php foot(); ?>
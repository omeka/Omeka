<?php head(); ?>
<style type="text/css" media="screen">
@import url('<?php layout_css(); ?>');
#item-select {float:left; width: 378px;}
#layout-all {float:right; width:378px;}

</style>

<script type="text/javascript" charset="utf-8">
	
	var paginate_uri = "<?php echo uri('exhibits/items'); ?>";
	
	Event.observe(window, 'load', function() {
		//Loads the items pagination
		
		
		//Retrieve the pagination through ajaxy goodness
		getPagination(paginate_uri, function() {
			//When you're done, make all the items drag/droppable
			dragDropForm();
			
			onLoadPagination();
		});
		
	});
	
	function onLoadPagination() 
	{
		new Effect.Highlight('item-list');
		
		//Make each of the pagination links fire an additional ajax request
		var links = $$('#pagination a');
		
		links.each(function(link) {
			link.onclick = function() {
				getPagination(this.href, onLoadPagination);
				return false;
			}
		});
		
		//Make the correct elements on the pagination draggable
		makeDraggable($$('#item-select div.item-drop'));
		
		//Disable the items in the pagination that are already used
		disablePaginationDraggables();
		
		//Hide all the numbers that tell us the Item ID
		var idDivs = document.getElementsByClassName('item_id');
		idDivs.each(function(el) {el.hide()});
		
		//Make the search form respond with ajax power
		$('search').onsubmit = function() {
			getPagination(paginate_uri, onLoadPagination, $('search').serialize());
			return false;
		}
	}
	
	function getPagination(uri, onFinish, parameters)
	{
		new Ajax.Updater('item-select', uri, {
			parameters: parameters,
			onComplete: onFinish
		});
	}
	
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
		
	<div id="item-select">
		
	</div>
	<p class="warning">(Warning: You must save the form before paginating through the items otherwise its contents may be erased)</p>
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
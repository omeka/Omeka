<?php head(); ?>
<style type="text/css" media="screen">
@import url('<?php layout_css(); ?>');
#page-builder {width:910px;margin:18px 0 18px 36px;}
#item-select {float:left; width: 378px; background:#DAE9F6; height:600px;}
#layout-all {float:right; width:512px;}

#layout-form .item {clear:both;overflow:hidden; border-bottom:1px solid #ccc; margin-bottom:1.8em; padding-bottom:1.8em;}
#layout-form .item-drop {width:100px; height:100px; float:left; overflow:hidden; border:2px solid #999; margin-right:2px; margin-bottom:2px;display:block;}

#item-list {margin-left:18px;}
#item-list .item-drop {font-size:1.2em; display:block;}
#layout-form .textfield {float:right; width:390px;}
#delete-page {clear:both;}
#layouts {position:relative;  padding-top:0;}
#layouts #layout-thumbs {padding-top:4em;}
#layout-thumbs .layout-name {display:none;}
#layouts #choose_layout {position:absolute; top:0; right:0;}
#layouts .layout {cursor:pointer;}


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
	
	Event.observe(window, 'load', makeLayoutSelectable);
	
	function makeLayoutSelectable() {
		var current_layout = $('current_layout');
		var layouts = document.getElementsByClassName('layout');
		
		//Make each layout clickable
		layouts.each( function(layout) {
			layout.onclick = function() {
				//Make a copy of the image
				var img = this.getElementsByTagName('img')[0];
				var copy = img.cloneNode(true);
				
				//Overwrite the contents of the div that displays the current layout
				current_layout.update();
				current_layout.appendChild(copy);
				new Effect.Highlight(current_layout);

				//Make sure the input is selected
				var input = this.getElementsByTagName('input')[0];
				input.click();
			}
		});		
	}
	
	function onLoadPagination() 
	{
		//new Effect.Highlight('item-list');
		
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
<div id="page-builder">
<?php if ( empty($page->layout) ): ?>
<form method="post" id="choose-layout">
	<fieldset>
		<button type="submit" name="exhibit_form" id="exhibit_form" class="exhibit-button">Exhibit Metadata</button>
		<button type="submit" name="section_form" id="section_form" class="exhibit-button">Section Metadata</button>
		
		<div id="page_button" class="exhibit-button">Page Metadata</div>
		
		<?php 
		//	submit('Exhibit', 'exhibit_form');
		//	submit('New Page', 'page_form'); 
		?>
		
	</fieldset>
	
	<fieldset id="layouts">
		<legend>Layouts</legend>
		<div id="current_layout"></div>
		<div id="layout-thumbs">
<?php 
	$layouts = get_ex_layouts();
	
	foreach ($layouts as $layout) {
		exhibit_layout($layout);
	} 
?>
</div>
<button type="submit" name="choose_layout" id="choose_layout" class="page-button">Choose a Layout</button>

	</fieldset> 
	
</form>

<?php else: ?>


	<?php 
		if(!$page->exists()) {
			$url = uri('exhibits/addPage').DIRECTORY_SEPARATOR.$section->id.DIRECTORY_SEPARATOR; 
		}else {
			$url = uri('exhibits/editPage').DIRECTORY_SEPARATOR.$page->id.DIRECTORY_SEPARATOR;
		}
		
	?>
		
	
	<p class="warning">(Warning: You must save the form before paginating through the items otherwise its contents may be erased)</p>
<form name="layout" id="page-form" method="post">
	<fieldset id="tertiary-nav">
		<button type="submit" name="exhibit_form" id="exhibit_form" class="exhibit-button">Exhibit Metadata</button>
		<button type="submit" name="section_form" id="section_form" class="exhibit-button">Section Metadata</button>
		
		<div id="page_button" class="exhibit-button" class="current">Page Metadata</div>
		
		<?php 
		//	submit('Exhibit', 'exhibit_form');
		//	submit('New Page', 'page_form'); 
	//	submit('Save &amp; Add Another Page', 'page_form');
		?>
			
	</fieldset>
	<fieldset>
			<button type="submit" name="page_form" id="page_form" class="page-button">Save and Add Another Page</button>
			<button type="submit" name="cancel" id="cancel_page" class="page-button">Cancel This Page</button>
	</fieldset>
	
	<div id="item-select">
		
	</div>
	
	<div id="layout-submits">
		
	<?php
	//	submit('Save Changes &amp; Continue Paginating Through Items', 'save_and_paginate'); 
	//	submit('Save &amp; Return to Exhibit', 'exhibit_form');
	//	submit('Save &amp; Return to Section', 'section_form');
	//	
	//	submit('Save &amp; Add Another Page', 'page_form');
	//	submit('Change the layout for this page', 'change_layout');	
	?>
	
	</div>
	<div id="layout-all">
	<div id="layout-form">
	<?php render_layout_form($page->layout); ?>
	</div>
	</div>
</form>
	<?php if ( $page->exists() ): ?>
		<form id="delete-page" action="<?php echo uri('exhibits/deletePage/'.$page->id); ?>">
		<?php submit('Cancel/Delete this page', 'delete_page'); ?>
		</form>
	<?php else: ?>
		<form id="delete-page" method="get">
			<?php submit('Cancel Adding This Page', 'cancel'); ?>
		</form>
	<?php endif; ?>
<?php endif; ?>
</div>
<?php foot(); ?>
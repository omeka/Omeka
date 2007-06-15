<?php head(); /*?>

<script type="text/javascript" charset="utf-8">
//<![CDATA[
	function getItemId(item) {
		var id = null;
		
		//item is a form element so return the value
		if(item.name) {
			return parseInt(item.value);
		}

		//Otherwise item is a div in item-select grab the item_id sub-div
		else {
			var field = item.getElementsByClassName('item_id')[0];
			id = parseInt(field.innerHTML.strip());
			return id;
		}
		return false;
	}
	
	function setItemId(input, id) {
		input.value = id;
	}
	
	function makeDroppable()
	{
		var formElements = $$("#layout-form .item");
		
		//Make the form fields droppable
		for (var i=0; i < formElements.length; i++) {
			var formInput = formElements[i];
			formElements[i].type = "hidden";
			
			var dropDiv = formElements[i].wrap('div','drop-item');
			Droppables.add(dropDiv, {
				accept: 'item',
				snap: true,
				onDrop: function(draggable, droppable) {
					//Set the itemId of the form element
					var itemId = getItemId(draggable);
					var oldItemId = getItemId(formInput);
					setItemId(formInput, itemId);

					replaceDraggable(draggable, droppable);
				}
			});
		};
	}
	
	function replaceDraggable(draggable, droppable)
	{
		var oldDraggable = droppable.getElementsBySelector("div.item").last();
		//replace the old one
		if( oldDraggable ) {
	    	droppable.replaceChild( draggable, oldDraggable) ;
			
			var containers = $$("#item-select .item-container");
			for (var i=0; i < containers.length; i++) {
				//If we match the old container with the old draggable item
				if(getItemId(containers[i]) == getItemId(oldDraggable)) {
					var oldContainer = containers[i];
					break;
				}
			};
			
			//Put the item back into the list if possible (check if there is already item)
			if(oldContainer && oldContainer.firstChild.className != 'item') {
				var oldItem_id = oldContainer.getElementsByClassName('item_id')[0];
				oldContainer.insertBefore(oldDraggable, oldItem_id);
			}
	
	  	}
	    else {
			droppable.appendChild(draggable);
		}
	}
	
	function makeDraggable()
	{
		var draggableItems = $$("#item-select .item");
		
		//Make the pagination items draggable
		for (var i=0; i < draggableItems.length; i++) {
			
			//wrap each of the draggable items in a container
			var container = draggableItems[i].wrap('div','item-container');
			
			//make sure you can obtain the item_id from the container
			var item_id = document.createElement('div');
			item_id.innerHTML = getItemId(draggableItems[i]);
			item_id.addClassName('item_id').hide();
			container.appendChild(item_id);
			
			//now make the items draggable
			var drag = new Draggable(draggableItems[i], {
				revert:true,
				ghosting:true
			});
		};			
	}
	
	function reloadPagination(event)
	{
		var page = event.target.rel;
		new Ajax.Updater('item-select', "<?php echo uri('exhibits/ajaxPagination'); ?>", {
			parameters: {
				page: page
			},
			onComplete: function(t) {
				ajaxPagination();
				makeDraggable();
			}
		});
		return false;
	}
	
	function showItemsOnForm()
	{
		var formElements = $$("#layout-form input.item");

		for (var i=0; i < formElements.length; i++) {
			var id = getItemId(formElements[i]);
			
			if(id) {
				var formInput = formElements[i];
			
				new Ajax.Request("<?php echo uri('exhibits/ajaxItemThumb'); ?>", {
					parameters: {
						id: id
					},
					onSuccess: function (t) {
						var html = t.responseText;
						new Insertion.After(formInput, html);
					}
				});				
			}

		};	
	}
	
	function ajaxPagination()
	{
		var links = $$("#pagination a");
		var baseUri = "<?php echo $_SERVER['REQUEST_URI']; ?>";

		for (var i=0; i < links.length; i++) {
			//Here's a hack for ya: grab the substring of the pagination url that represents page number
			var href = links[i].getAttribute('href');
			href = href.sub(baseUri+'/', '');
			
			//Kill all the links
			links[i].setAttribute('rel', href);
			links[i].setAttribute('href', 'javascript:void(0)');
			
			//Make the onclick that does ajax call
			links[i].onclick = reloadPagination;
		};
	}
	
	Event.observe(window, 'load', function() {
		makeDroppable();
		showItemsOnForm();
		makeDraggable();
		ajaxPagination();
		
		//Get rid of the warning
		document.getElementsByClassName('warning')[0].hide();
	});
//]]>
</script>
*/ ?>
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
	$retVal = _make_omeka_request('Items','browse',array('pagination_url'=>$url, 'public'=>true),array('items','pagination'));
	extract($retVal);
	
	?>
	<div id="pagination">
	<?php 
		 echo pagination(); 
	?>
		</div>
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
	
		
	</div>
	
	<p class="warning">(Warning: You must save the form before paginating through the items otherwise its contents may be erased)</p>

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

<?php foot(); ?>
function getItemId(element) {
	var id = null;
	if(!element) {
		throw 'Invalid element passed to getItemId()';
	}
	//item is a form element so return the value
	if(element.hasClassName('item-drag')) {

		return getItemIdFromSubDiv(element);		
	}
	else if( element.hasClassName('item-drop') ) {
		//try to get itemId from the input in the div
		id = getItemIdFromInput(element);
		if(id !== false) {
			return id;
		} else {
			return getItemIdFromSubDiv(element);
		}
		
	}else {
		throw 'Invalid element with class = ' + element.className + ' has been passed to getItemId()';
	}

	return false;
}

function getItemIdFromSubDiv(element) {
	var idDiv = element.getElementsByClassName('item_id')[0];
	return parseInt(idDiv.innerHTML.strip());
}

function getItemIdFromInput(element) {
	var input = element.getElementsByTagName('input')[0];

	if(!input) {
		return false;
	}
	return input.value;
}

function setItemId(element, id) {
	if(element.hasClassName('item-drop')) {
		var input = element.getElementsByTagName('input')[0];
		if(input) {
			input.value = id;
		}
	}else if(element.hasClassName('item-drag')) {
		var idDiv = element.getElementsByClassName('item_id')[0];
		idDiv.innerHTML = id;
	}else {
		throw 'Element passed to setItemId() has className of '+element.className;
	}
}

function getItemFromContainer(container) {
	var item = container.getElementsBySelector('div.item-drag').last();
	if(item) {
		return item;
	}else {
		return false;
	}
}

function sendItemHome(draggable) {
		var draggableId = getItemId(draggable);
		
		//Loop through a list of the containers in the pagination
		//Check the itemId of each for a match
		var containers = $$('#item-select .item-drop');
		var reAttached = false;
		
		for (var i=0; i < containers.length; i++) {
			var containerItemId = getItemId(containers[i]);
			var container = containers[i];
			
			if(containerItemId == draggableId) {
				//Check if there is already an item in this spot
				if(!getItemFromContainer(container)) {
					reAttached = true;
					container.appendChild(draggable);
				}
			}
		};
		
		if(!reAttached) {
			draggable.destroy();
		}
			
}

function moveDraggable(draggable, droppable) {
	/* 	Step 1: Get the itemId for the newly dropped item
		Step 2: Get the existing item
			Step 3: If there is an existing item, move it back to its original spot
				Step 4: If there is already a droppable item in its original spot, destroy it
		Step 5: Move the newly dropped item inside the droppable container
		Step 6: Change the value of the container's form element to reflect the new itemId */
	var newItemId = getItemId(draggable);
		
	var oldItem = getItemFromContainer(droppable);
	
	if(oldItem) {
		sendItemHome(oldItem);
	}
	
	//append and set the itemId of the droppable element
	droppable.appendChild(draggable);
}

//Get a list of the draggables on the form
//Fade the image and unregister the ones that are on the pagination
function disablePaginationDraggables() {
	var formContainers = $$('#layout-form div.item-drop');
	var paginationContainers = $$('#item-select div.item-drop');
	
	for (var i=0; i < formContainers.length; i++) {
		if(id = getItemId(formContainers[i])) {
			for (var j=0; j < paginationContainers.length; j++) {
				var paginationId = getItemId(paginationContainers[j]);
				if(paginationId == id) {
					
					//Disable the draggable
					var itemToDestroy = getItemFromContainer(paginationContainers[j]);
					if(itemToDestroy) {
						itemToDestroy.destroy();
					}
					break;
				}
			};
		}
	};
}

function makeDraggable(containers) {
	var options = {
		revert:true,
		handle: 'handle',
		snap: [20, 20],
		onStart: function(draggable, event) {			
			if(draggable.element.descendantOf('layout-form')) {
				var oldContainer = draggable.element.parentNode;
				setItemId(oldContainer, '');
			}
		},
		onEnd: function(draggable) {
			var droppable = draggable.element.parentNode;
			if(droppable.descendantOf('layout-form')) {
				setItemId(droppable, getItemId(draggable.element));
			}
		}
	}
	
	for (var i=0; i < containers.length; i++) {
		
		var item = getItemFromContainer(containers[i]);
		if(item) {
			var drag = new Draggable(item, options);
		}
	};
}

function makeDroppable(containers, onDrop) {
	for (var i=0; i < containers.length; i++) {
		Droppables.add(containers[i], {
			snap: true,
			onDrop: onDrop
		});
	};
}

function dragDropForm() {
	var formContainers = $$('#layout-form div.item-drop');
	
	//All items are draggable but only items on the form will reset the form inputs when dragged
	makeDraggable(formContainers);
	
	//Dropping the items on the form should only work when dropping them elsewhere on the form
	makeDroppable(formContainers, function(draggable, droppable) {
		moveDraggable(draggable, droppable);
		$$('#layout-form .handle').invoke('hide');

		new Effect.Highlight(droppable);
	});
	
	//Add a little 'clear' button to the top of each item-drop div
	formContainers.each( function(drop) {
		var clear = document.createElement('a');
		clear.innerHTML = "Remove This Item";
		clear.className = 'remove_item';
		clear.style.cursor = "pointer";
		clear.onclick = function() {
			var drag = getItemFromContainer(drop);
			setItemId(drop, '');
			if(drag) {
				sendItemHome(drag);
				$$('#item-list .handle').invoke('show');
			}
		}
		drop.appendChild(clear);
	});
	
	//Hide the item_id divs
	document.getElementsByClassName('item_id').each( function(el) {
		el.hide();
	});
	
	//Hide the labels for the item boxes
	$$('.item-drop label').invoke('hide');
	
	//Hide the text fields for the layout form
	$$('.item-drop input').invoke('hide');
}

//Hide buttons for the non-js version of the exhibits builder
function hideReorderSections() {
	if(!$('reorder_sections')) return;
	$('reorder_sections').remove();	
}

//Hide more buttons for the non-js version of the exhibits builder
function hideReorderExhibits() {
	if(!$('reorder_exhibits')) return;
	$('reorder_exhibits').remove();	
}

//Style the handles for the section/page lists in the exhibit builder
function styleExhibitBuilder() {
	hideReorderSections();
	hideReorderExhibits();
	
	var handles = $$('.handle');
	for(var i=0; i<handles.length; i++) {
		handles[i].setStyle({display:'inline',cursor: 'move'});
	}
		
	var orderInputs = $$('.order-input');
	for(var i=0; i<orderInputs.length; i++) {
		orderInputs[i].setStyle({border: 'none',background:'#fff',color: '#333'});
	}	
}
Event.observe(window,'load',styleExhibitBuilder);
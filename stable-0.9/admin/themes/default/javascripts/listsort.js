function makeSortable(list) {
	var opt = {tag: listSorter.tag, onUpdate: reorderList,ghosting: false};
	
	if(listSorter.handle) {
		opt.handle = listSorter.handle;
	}	
	if(listSorter.overlap) {
		opt.overlap = listSorter.overlap;
	}
	if(listSorter.constraint) {
		opt.constraint = listSorter.constraint;
	}
//	Sortable.destroy(list);
	
	Sortable.create(list, opt);
	enableListForm(false);
	
	//Auto-update the form when someone clicks a delete link
	var dl = $$(listSorter.deleteLinks);
	dl.each(function(e) {e.onclick = ajaxListDelete; });
	
	//When we submit the form, then enable the elements in the list so that they submit properly
	Event.observe(listSorter.form, 'submit', function() {
		enableListForm(true);
	});
}

//Enable or disable the section part of the form (depending)
function enableListForm(enable) {
	var orderInputs = $A(listSorter.list.getElementsByTagName('input'));
	
	if(enable == true) {
		orderInputs.each(function(el) {el.enable();} );
	}else {
		orderInputs.each(function(el) {el.disable();} );
	}
}

function reorderList(container) {
	var elements = container.getElementsByTagName(listSorter.tag);
	for (var i=0; i < elements.length; i++) {
		element = elements[i];
		var input = elements[i].getElementsByTagName('input')[0];
		var order = i+1;
		input.value = order;
	};
}

function ajaxListDelete(event) {
		var href = this.href;

		if(confirm(listSorter.confirmation)) {
		
			new Ajax.Request(href, {
				method:'get',
				onSuccess: function(t) {
					if(listSorter.partialUri) {
						new Ajax.Request(listSorter.partialUri, 
						{
							parameters: 'id=' + listSorter.recordId,
							onComplete: function(t) {
								listSorter.list.updateAppear(t.responseText);
								makeSortable(listSorter.list);
								if(listSorter.callback) {
									listSorter.callback();
								}
							}
						} );
					}
				},
				onFailure: function(t) {
					alert(t.status);
				}
			});	
		}
		
		return false;
}
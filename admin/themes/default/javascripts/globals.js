var Omeka = {
	flash: function(msg) {
		var div = $('alert');
		if(div == null) {
			var form = $$('form').first();
			new Insertion.Before(form, '<div id="alert"></div>');
			div = $('alert');
		}

		div.updateAppear(msg);
		div.setStyle({display:'block'});
		new Effect.Highlight(div, {duration:'2.0',startcolor:'#ffff99', endcolor:'#ffffff'});
	},
	
	hideFlash: function() {
		$('alert').hide();
	}
};

function alertBox() {
	var alerts = $$('div.alert');
	for(var i=0;i<alerts.length;i++) {
		alert = alerts[i];
		new Effect.Highlight(alert, {duration:'2.0',startcolor:'#ffff99', endcolor:'#ffffff'});
		return;
		//new Effect.Fade(alert,{duration:'6.0'});
		//return;
	}
}

function revealSwitch( field, file ) {
	new Ajax.Updater(field,
		revealPath,
		{
			parameters: {
				view: file
			},
			onComplete: function(t) {
				new Effect.Appear( field, {duration: 0.8} );
			}
		});
}

function toggleSearch() {
	//alert('foo');
	var search = $('search');
	if(!search) return;
	search.hide();
	var searchHeader = $('search-header');
	if(!searchHeader) return;
	searchHeader.style.cursor = "pointer";
	searchHeader.onclick = function() {
		new Effect.toggle(search,'blind',{duration:0.5});
		searchHeader.toggleClassName('open','close');
		return false;
	}
}

function hideReorderSections() {
	if(!$('reorder_sections')) return;
	$('reorder_sections').remove();	
}

function hideReorderExhibits() {
	if(!$('reorder_exhibits')) return;
	$('reorder_exhibits').remove();	
}

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

function roundCorners() {
	Nifty('#primary-nav a,#secondary-nav a','top transparent');
	Nifty('#user-meta','bottom big');
	Nifty('#site-info,#type-items,#getting-started ul');
	Nifty('#view-all-items a','transparent');
	Nifty('#search,#names-add,div.meta ul','big');
	Nifty('#add-item,#add-collection,#add-type,#add-user,#add-file,#add-exhibit','transparent');
}

function makeAccordion() {
	if(!$('item-form')) return;
	var accordionForm = new accordion('#item-form', {
	resizeSpeed : 8,
	classNames : {
	    toggle : 'toggle',
	    toggleActive : 'toggle_active',
	    content : 'toggle_content'
	}
	});
	
	var toggleHeader = $$('#item-form .toggle');
	
	for(var i=0;i<toggleHeader.length; i++) {
	toggleHeader[i].setStyle({cursor: 'pointer'});
	}
	
	accordionForm.activate($$('#item-form .toggle')[0]);
}

function checkDeleteItem() {
	if(!$('delete_item')) return;
	$('delete_item').onclick = function() {
		return confirm('Are you sure you want to delete this item, including all of the files, tags, and other data associated with the item, from the archive?' );
	}
}

function filesAdding()
{
	if(!$('add-more-files')) return;
	var nonJsFormDiv = $('add-more-files');
	
	//This is where we put the new file inputs
	var filesDiv = $$('#file-inputs .files')[0];
	var filesDivWrap = $('file-inputs');
	//Make a link that will add another file input to the page
	var link = Builder.node('a', {href:'javascript:void(0);',id:'add-file',class:'add-file'}, 'Add another file');

	Event.observe(link, 'click', function(){
		var inputs = $A(filesDiv.getElementsByTagName('input'));
		var inputCount = inputs.length;
		var fileHtml = '<div id="fileinput'+inputCount+'" class="fileinput"><input name="file['+inputCount+']" id="file['+inputCount+']" type="file" class="fileinput" /></div>';
		new Insertion.After(inputs.last(), fileHtml);
		$('fileinput'+inputCount).hide();
		new Effect.SlideDown('fileinput'+inputCount,{duration:0.2});
		//new Effect.Highlight('file['+inputCount+']');
	});
	
	nonJsFormDiv.update();
	filesDivWrap.appendChild(link);
}

Event.observe(window,'load',roundCorners);
Event.observe(window,'load',filesAdding);
Event.observe(window,'load',checkDeleteItem);
Event.observe(window,'load',toggleSearch);
Event.observe(window,'load',alertBox);
Event.observe(window,'load',styleExhibitBuilder);
//Event.observe(window,'load',makeAccordion);
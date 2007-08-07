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
		revealPath + file,
		{
			onComplete: function(t) {
				new Effect.Appear( field, {duration: 0.8} );
			}
		});
}

function toggleSearch() {
	var search = $('search');
	if(!search) return;
	search.hide();
	var searchHeader = $('search-header');
	if(!searchHeader) return;
	searchHeader.style.cursor = "pointer";
	searchHeader.onclick = function() {
		search.toggle();
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
	Nifty('#primary-nav a','top transparent');
	Nifty('#secondary-nav a','top transparent');
	Nifty('#user-meta','bottom big');
	Nifty('#site-info');
	Nifty('#getting-started ul');
	Nifty('#view-all-items a','transparent');
	Nifty('#search','big');
	Nifty('#names-add','big');
	Nifty('div.meta ul','big');
	Nifty('#add-item,#add-collection,#add-type','transparent');
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

Event.observe(window,'load',toggleSearch);
Event.observe(window,'load',alertBox);
Event.observe(window,'load',styleExhibitBuilder);
Event.observe(window,'load',roundCorners);
//Event.observe(window,'load',makeAccordion);
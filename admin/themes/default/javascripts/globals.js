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
	Nifty('#user-meta','bottom');
}

Event.observe(window,'load',toggleSearch);
Event.observe(window,'load',alertBox);
Event.observe(window,'load',styleExhibitBuilder);
Event.observe(window,'load',roundCorners);
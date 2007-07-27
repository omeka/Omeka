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

Event.observe(window,'load',toggleSearch);
Event.observe(window,'load',alertBox);
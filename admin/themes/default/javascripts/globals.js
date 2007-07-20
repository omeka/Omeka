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
function toggleSearch() {
	var search = $('search');
	search.hide();
	var searchHeader = $('search-header');
	searchHeader.style.cursor = "pointer";
	searchHeader.onclick = function() {
		search.toggle();
		searchHeader.toggleClassName('open','close');
		return false;
	}
}

Event.observe(window,'load',toggleSearch);
Event.observe(window,'load',alertBox);
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

Event.observe(window,'load',alertBox);
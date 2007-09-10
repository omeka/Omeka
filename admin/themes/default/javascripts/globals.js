//Namespace for generic Omeka utility functions
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

//Highlight 'alert' boxes after the page has loaded (different from Omeka.flash)
function alertBox() {
	var alerts = $$('div.alert');
	
	for(var i=0;i<alerts.length;i++) {
		alert = alerts[i];
		alert.style.background = "white";
		new Effect.Highlight(alert, {duration:'3.0',startcolor:'#ffff99', endcolor:'#ffffff'});
	//	new Effect.Fade(alert,{duration:'6.0'});
		return;
	}
}

//Adds rounded corners to the admin theme
function roundCorners() {
	Nifty('#primary-nav a,#secondary-nav a','top transparent');
	Nifty('#view-style a','top transparent');
	Nifty('#user-meta','bottom big');
	Nifty('#site-meta,#recent-items,#tag-cloud,#type-items,#getting-started ul');
	Nifty('#view-all-items a','transparent');
	Nifty('#search,#names-add','big');
	Nifty('#add-item,#add-collection,#add-type,#add-user,#add-file,#add-exhibit,#new-user-form','transparent');
}

//This will add confirmation for deleting files and the item
function confirmDelete() {
	$$('.delete').each( function(el) {
		el.onclick = function() {
			return confirm('Are you sure you want to delete this?');
		}
	});
	$$('.delete-exhibit').each(function(el) {
		el.onclick = function() {
			return confirm( 'Are you sure you want to delete this exhibit and all of its data from the archive?' );
		}
	});
}
Event.observe(window,'load',roundCorners);
Event.observe(window,'load',alertBox);
Event.observe(window,'load',confirmDelete);

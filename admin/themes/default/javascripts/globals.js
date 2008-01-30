//Namespace for generic Omeka utility functions
var Omeka = {
	flash: function(msg, status) {
		
		if(typeof status == 'undefined') {
			status = 'alert';
		}
		
		var div = $('alert');
		if(div == null) {
			var form = $$('form').first();
			new Insertion.Before(form, '<div id="alert" class="' + status + '"></div>');
			div = $('alert');
		}else {
			div.className = status;
		}

		div.updateAppear(msg);
		div.setStyle({display:'block'});
//		new Effect.Highlight(div, {duration:'2.0',startcolor:'#ffff99', endcolor:'#ffffff'});
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
	Nifty('#view-style a,#browse-meta','top transparent');
	Nifty('#user-meta','bottom big');
	Nifty('#login #content,#site-meta,#recent-items,#tag-cloud,#type-items,#getting-started ul');
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

	
	const Person = "Person";
	const Institution = "Institution";
	
	
	
	function switchForm(radio) {
		if(!document.getElementById) return;
		var personElements = ['first_name','middle_name', 'last_name'];
		var institutionElements = ['institution'];
		
		if(radio.value == Institution) {
			//Disable name elements on form
			personElements.each(function(el) {
				var element = $(el);
				ancestors = element.ancestors();
				ancestors[0].hide();
				element.disable();
				element.hide();
			});
			institutionElements.each(function(el) {
				var element = $(el);
				element.enable();
				element.show();
				ancestors = element.ancestors();
				ancestors[0].show();
			});
		}else{
			//Enable name elements
			personElements.each(function(el) {
				var element = $(el);
				element.enable();
				element.show();
				ancestors = element.ancestors();
				ancestors[0].show();
			});
			institutionElements.each(function(el) {
				var element = $(el);
				element.disable();
				element.hide();
				ancestors = element.ancestors();
				ancestors[0].hide();
			
			});
		}
	}
	
function toggleNamesForm() {
	if(!document.getElementById) return;
	if(!$('name-inputs') || !$('entity-type')) return;
	var radioButtons = $$("#entity-type input");
	var allFields = $('name-inputs');
	allFields.hide();
	for (var i=0; i < radioButtons.length; i++) {
		radioButtons[i].onclick = function() {
			switchForm(this);
			allFields.show();
		};
		if(radioButtons[i].checked) {
			switchForm(radioButtons[i]);
			allFields.show();
		}
	}
}

Event.observe(window,'load',toggleNamesForm);
Event.observe(window,'load',roundCorners);
Event.observe(window,'load',alertBox);
Event.observe(window,'load',confirmDelete);

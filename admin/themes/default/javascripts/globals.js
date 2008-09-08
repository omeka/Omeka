//Namespace for generic Omeka utility functions
if(typeof Omeka == 'undefined') {
	Omeka = {};
}
Omeka.flash = function(msg, status) {
		
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
};
	
Omeka.hideFlash = function() {
		$('alert').hide();
};

//Highlight 'alert' boxes after the page has loaded (different from Omeka.flash)
Omeka.alertBox = function() {
	var alerts = $$('div.alert');
	
	for(var i=0;i<alerts.length;i++) {
		alert = alerts[i];
		alert.style.background = "white";
		new Effect.Highlight(alert, {duration:'3.0',startcolor:'#ffff99', endcolor:'#ffffff'});
	//	new Effect.Fade(alert,{duration:'6.0'});
		return;
	}
}

//This will add confirmation for deleting files and the item
function confirmDelete() {
	$$('.delete').each( function(el) {
		el.onclick = function() {
			return confirm('Are you sure you want to delete this?');
		}
	});
}

Omeka.Form = Object.extend({}, {
    /* Loop through all the spans with class="tooltip" and make them visible 
	as tooltips */
    makeTooltips: function(tooltipElements) {        
		tooltipElements.each(function(span){
		   //The div that wraps the tooltip and the form element
		   var div = span.up();

		   var image = span.previous('img');
		   if (!image) {
		       return;
		   };
		   image.style.cursor = "help";
		   
		   // Insert the informational image right after the label for the field
		   div.select('label').first().insert({after:image});
		   
		   var tooltip = new Tooltip(image, span, 
		       {default_css:true, zindex:100000});
		   span.addClassName('info-window');
		}.bind(this));        
    }
});
	
Omeka.switchForm = function(radio) {
		var Person = "Person";
		var Institution = "Institution";

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
	
Omeka.toggleNamesForm = function() {
	if(!document.getElementById) return;
	if(!$('name-inputs') || !$('entity-type')) return;
	var radioButtons = $$("#entity-type input");
	var allFields = $('name-inputs');
	allFields.hide();
	for (var i=0; i < radioButtons.length; i++) {
		radioButtons[i].onclick = function() {
			Omeka.switchForm(this);
			allFields.show();
		};
		if(radioButtons[i].checked) {
			Omeka.switchForm(radioButtons[i]);
			allFields.show();
		}
	}
}

Event.observe(window,'load',Omeka.toggleNamesForm);
Event.observe(window,'load',Omeka.alertBox);
Event.observe(window,'load',confirmDelete);

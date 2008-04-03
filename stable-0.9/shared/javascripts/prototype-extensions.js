//Adapted from an example in the Prototype API PDF

Element.addMethods({ 
	wrap: function(element, tagName, className) { 
		element = $(element); 
		var wrapper = document.createElement(tagName); 
		element.parentNode.replaceChild(wrapper, element); 
		wrapper.appendChild(element); 
		wrapper.addClassName(className);
		return Element.extend(wrapper); 
	},
	
	insertAfter: function(element, insert) { insert.parentNode.insertBefore(element, insert.nextSibling); },
	
	destroy: function(element) {var parent = element.up(); parent.removeChild(element); },
	
	updateAppear: function(element, text) {
		element.hide();
		element.update(text);
		Effect.Appear(element, {duration: 1.0});
	}
}); 

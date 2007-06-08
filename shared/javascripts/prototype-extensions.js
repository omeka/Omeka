//Adapted from an example in the Prototype API PDF

Element.addMethods({ 
	wrap: function(element, tagName, className) { 
		element = $(element); 
		var wrapper = document.createElement(tagName); 
		element.parentNode.replaceChild(wrapper, element); 
		wrapper.appendChild(element); 
		wrapper.addClassName(className);
		return Element.extend(wrapper); 
	} 
}); 

/*
cbb function by Roger Johansson, http://www.456bereastreet.com/
*/
var cbb = {
	init : function() {
	// Check that the browser supports the DOM methods used
		if (!document.getElementById || !document.createElement || !document.appendChild) return false;
		var oElement, oOuter, oI1, oI2, tempId;
	// Find all elements with a class name of cbb
		var arrElements = document.getElementsByTagName('*');
		var oRegExp = new RegExp("(^|\\s)cbb(\\s|$)");
		for (var i=0; i<arrElements.length; i++) {
	// Save the original outer element for later
			oElement = arrElements[i];
			if (oRegExp.test(oElement.className)) {
	// 	Create a new element and give it the original element's class name(s) while replacing 'cbb' with 'cb'
				oOuter = document.createElement('div');
				oOuter.className = oElement.className.replace(oRegExp, '$1cb$2');
	// Give the new div the original element's id if it has one
				if (oElement.getAttribute("id")) {
					tempId = oElement.id;
					oElement.removeAttribute('id');
					oOuter.setAttribute('id', '');
					oOuter.id = tempId;
				}
	// Change the original element's class name and replace it with the new div
				oElement.className = 'i3';
				oElement.parentNode.replaceChild(oOuter, oElement);
	// Create two new div elements and insert them into the outermost div
				oI1 = document.createElement('div');
				oI1.className = 'i1';
				oOuter.appendChild(oI1);
				oI2 = document.createElement('div');
				oI2.className = 'i2';
				oI1.appendChild(oI2);
	// Insert the original element
				oI2.appendChild(oElement);
	// Insert the top and bottom divs
				cbb.insertTop(oOuter);
				cbb.insertBottom(oOuter);
			}
		}
	},
	insertTop : function(obj) {
		var oOuter, oInner;
	// Create the two div elements needed for the top of the box
		oOuter=document.createElement("div");
		oOuter.className="bt"; // The outer div needs a class name
	    oInner=document.createElement("div");
	    oOuter.appendChild(oInner);
		obj.insertBefore(oOuter,obj.firstChild);
	},
	insertBottom : function(obj) {
		var oOuter, oInner;
	// Create the two div elements needed for the bottom of the box
		oOuter=document.createElement("div");
		oOuter.className="bb"; // The outer div needs a class name
	    oInner=document.createElement("div");
	    oOuter.appendChild(oInner);
		obj.appendChild(oOuter);
	},
	// addEvent function from http://www.quirksmode.org/blog/archives/2005/10/_and_the_winner_1.html
	addEvent : function(obj, type, fn) {
		if (obj.addEventListener)
			obj.addEventListener(type, fn, false);
		else if (obj.attachEvent) {
			obj["e"+type+fn] = fn;
			obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
			obj.attachEvent("on"+type, obj[type+fn]);
		}
	}
};

cbb.addEvent(window, 'load', cbb.init);
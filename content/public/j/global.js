function addLoadListener(fn) {
	if (typeof window.addEventListener != 'undefined') {
		window.addEventListener('load', fn, false);
	}
	else if (typeof document.addEventListener != 'undefined') {
		document.addEventListener('load', fn, false);
	}
	else if (typeof window.attachEvent != 'undefined') {
		window.attachEvent('onload', fn);
	}
	else {
		return false;
	}
	return true;
}

// Nice function that helps you add alternating classes to any number of child elements.
function striper(parentElementTag, parentElementClass, childElementTag, styleClasses)
{
	var i=0,currentParent,currentChild;
	// capability and sanity check
	if ((document.getElementsByTagName)&&(parentElementTag)&&(childElementTag)&&(styleClasses)) {
		// turn the comma separate list of classes into an array
		var styles = styleClasses.split(',');
		// get an array of all parent tags
		var parentItems = document.getElementsByTagName(parentElementTag);
		// loop through all parent elements
		while (currentParent = parentItems[i++]) {
			// if parentElementClass was null, or if the current parent's class matches the specified class
			if ((parentElementClass == null)||(currentParent.className == parentElementClass)) {
				var j=0,k=0;
				// get all child elements in the current parent element
				var childItems = currentParent.getElementsByTagName(childElementTag);
				// loop through all child elements
				while (currentChild = childItems[j++]) {
					// based on the current element and the number of styles in the array, work out which class to apply
					k = (j+(styles.length-1)) % styles.length;
					// add the class to the child element - if any other classes were already present, they're kept intact
					currentChild.className = currentChild.className+" "+styles[k];
				}
			}
		}
	}
}

function getElementsByAttribute(attribute, attributeValue) {
	var elementArray = new Array();
	var matchedArray = new Array();

	if (document.all) {
		elementArray = document.all;
	}
	else {
		elementArray = document.getElementsByTagName("*");
	}

	for (var i = 0; i < elementArray.length; i++) {
		if (attribute == "class") {
			var pattern = new RegExp("(^| )" + attributeValue + "( |$)");

			if (pattern.test(elementArray[i].className)) {
				matchedArray[matchedArray.length] = elementArray[i];
			}
		}
		else if (attribute == "for") {
			if (elementArray[i].getAttribute("htmlFor") || elementArray[i].getAttribute("for")) {
				if (elementArray[i].htmlFor == attributeValue) {
					matchedArray[matchedArray.length] = elementArray[i];
				}
			}
		}
		else if (elementArray[i].getAttribute(attribute) == attributeValue) {
			matchedArray[matchedArray.length] = elementArray[i];
		}
	}

	return matchedArray;
}

function makePopup(url, width, height, overflow)
{
  // if (width > 640) { width = 640; }
 //  if (height > 480) { height = 480; }

  if (overflow == '' || !/^(scroll|resize|both)$/.test(overflow))
  {
    overflow = 'both';
  }

  var win = window.open(url, '',
      'width=' + width + ',height=' + height
      + ',scrollbars=' + (/^(scroll|both)$/.test(overflow) ? 'yes' : 'no')
      + ',resizable=' + (/^(resize|both)$/.test(overflow) ? 'yes' : 'no')
      + ',status=yes,toolbar=no,menubar=no,location=no'
  );

  return win;
}


// Begin popup window function.
function popUps() {
	var links = getElementsByAttribute("class","popup");
	for (var i=0; i<links.length; i++) {
		link = links[i];
		link.onclick = function() {
			var popup = makePopup(this.href, 600, 400, 'both');
			return popup.closed;
		}  
	}	
}

function stripeObjects() {
	striper("div","stripe","div","odd,even");
}

function roundCorners() {
	Nifty("ul#mainnav a","medium transparent top");
	Nifty("ul#taglist","medium");
	Nifty("#addyourvoice-blurb,#about-blurb,#login-box,div#tagcloud","big");
	Nifty("#internalnav","big");
	Nifty("#contribute-intro","big");
	Nifty("ul.subnav a","transparent");
	Nifty("#jwa-contact","big");
	Nifty("ul#faqs","big");
	Nifty("div.object,div.featured-object,#mytags","medium");
}

addLoadListener(roundCorners);
addLoadListener(popUps);
addLoadListener(stripeObjects);
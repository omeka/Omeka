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

// NiftyCorners
function roundCorners() {
//	Nifty("ul#mainnav a","top transparent medium");
	Nifty("div#content","tl transparent medium");
	Nifty("div#footer","bottom transparent medium");
	Nifty("div#addyourphotos","tl transparent medium");
	Nifty("#tellyourstory","transparent medium");
	Nifty("#quickinfo","bottom transparent medium");
//	Nifty("#quickcontribute","transparent medium");
	Nifty("#newsfeed","transparent medium");
}

// Reusable Scriptaculous functions.
function reveal(elem)
{
	if( document.getElementById(elem).style.display == 'none' )
	{
		Effect.BlindDown( elem, {duration: 1});	
	}
}

function hide(elem)
{
	if( document.getElementById(elem).style.display != 'none' )
	{
		Effect.BlindUp(elem, {duration: 0.6});
	}
}

function switchXbox( state, id )
{
	if( state )
	{
		document.getElementById(id).checked = false;
	}
}


/*function revealSwitch( field, file )
{
	alert('<?php echo $_link->to(); ?>' + file);
	new Ajax.Updater(	field,
						'<?php echo $_link->to(); ?>' + file,
						{
						onComplete: function(t) {
							new Effect.BlindDown( field, {duration: 0.8} );
						}
						});
}*/

// addFile function adds an input under the "Add a File" section on the contribute form.
function addFile()
{
	var filelist = document.getElementById('files');
	var input = document.createElement('li');
	//input.style.display = "none";
	filelist.appendChild( input );
	
	input.className = 'foo';
	input.innerHTML = '<input name="objectfile[]" type="file" /><a href="javascript:void(0);" onclick="removeFile( parentNode )">Remove</a>';
	
	
	//Effect.Appear( input, {duration: 0.4} );
}

// removeFile removes the input under the "Add a File" section on the contribute form.
function removeFile( node )
{
//	Effect.Fade( node, {duration: 0.4} );
/*  setTimeout( function() { */document.getElementById('files').removeChild( node );/* }, 600);*/
}

// Load Listeners
addLoadListener(roundCorners);
addLoadListener(popUps);

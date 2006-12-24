function ss_fixAllLinks() {
 // Get a list of all links in the page
 var allLinks = document.getElementsByTagName('a');
 // Walk through the list
 for (var i=0;i<allLinks.length;i++) {
   var lnk = allLinks[i];
   if ((lnk.href && lnk.href.indexOf('#') != -1) &&  
       ( (lnk.pathname == location.pathname) ||
   ('/'+lnk.pathname == location.pathname) ) &&  
       (lnk.search == location.search)) {
     // If the link is internal to the page (begins in #)
     // then attach the smoothScroll function as an onclick
     // event handler
     ss_addEvent(lnk,'click',smoothScroll);
   }
 }
}

function smoothScroll(e) {
 // This is an event handler; get the clicked on element,
 // in a cross-browser fashion
 if (window.event) {
   target = window.event.srcElement;
 } else if (e) {
   target = e.target;
 } else return;
 
 // Make sure that the target is an element, not a text node
 // within an element
 if (target.nodeType == 3) {
   target = target.parentNode;
 }
 
 // Paranoia; check this is an A tag
 if (target.nodeName.toLowerCase() != 'a') return;
 
 // Find the <a name> tag corresponding to this href
 // First strip off the hash (first character)
 anchor = target.hash.substr(1);
 // Now loop all A tags until we find one with that name
 var allLinks = document.getElementsByTagName('a');
 var destinationLink = null;
 for (var i=0;i<allLinks.length;i++) {
   var lnk = allLinks[i];
   if (lnk.id && (lnk.id == anchor)) {
     destinationLink = lnk;
     break;
   }
 }
 
 // If we didn't find a destination, give up and let the browser do
 // its thing
 if (!destinationLink) return true;
 
 // Find the destination's position
 var destx = destinationLink.offsetLeft;  
 var desty = destinationLink.offsetTop;
 var thisNode = destinationLink;
 while (thisNode.offsetParent &&  
       (thisNode.offsetParent != document.body)) {
   thisNode = thisNode.offsetParent;
   destx += thisNode.offsetLeft;
   desty += thisNode.offsetTop;
 }
 
 // Stop any current scrolling
 clearInterval(ss_INTERVAL);
 
 cypos = ss_getCurrentYPos();
 
 ss_stepsize = parseInt((desty-cypos)/ss_STEPS);
 ss_INTERVAL = setInterval('ss_scrollWindow('+ss_stepsize+','+desty+',"'+anchor+'")',10);
 
 // And stop the actual click happening
 if (window.event) {
   window.event.cancelBubble = true;
   window.event.returnValue = false;
 }
 if (e && e.preventDefault && e.stopPropagation) {
   e.preventDefault();
   e.stopPropagation();
 }
}

function ss_scrollWindow(scramount,dest,anchor) {
 wascypos = ss_getCurrentYPos();
 isAbove = (wascypos < dest);
 window.scrollTo(0,wascypos + scramount);
 iscypos = ss_getCurrentYPos();
 isAboveNow = (iscypos < dest);
 if ((isAbove != isAboveNow) || (wascypos == iscypos)) {
   // if we've just scrolled past the destination, or
   // we haven't moved from the last scroll (i.e., we're at the
   // bottom of the page) then scroll exactly to the link
   window.scrollTo(0,dest);
   // cancel the repeating timer
   clearInterval(ss_INTERVAL);
   // and jump to the link directly so the URL's right
   location.hash = anchor;
 }
}

function ss_getCurrentYPos() {
 if (document.body && document.body.scrollTop)
   return document.body.scrollTop;
 if (document.documentElement && document.documentElement.scrollTop)
   return document.documentElement.scrollTop;
 if (window.pageYOffset)
   return window.pageYOffset;
 return 0;
}

function ss_addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
 if (elm.addEventListener){
   elm.addEventListener(evType, fn, useCapture);
   return true;
 } else if (elm.attachEvent){
   var r = elm.attachEvent("on"+evType, fn);
   return r;
 }
}  

var ss_INTERVAL;
var ss_STEPS = 25;

ss_addEvent(window,"load",ss_fixAllLinks);
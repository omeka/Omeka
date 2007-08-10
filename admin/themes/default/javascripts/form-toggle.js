/*-----------------------------------------------------------
    Toggles element's display value
    Input: any number of element id's
    Output: none 
    ---------------------------------------------------------*/
function toggleDisp() {
    for (var i=0;i<arguments.length;i++){
        var d = $(arguments[i]);
        if (d.style.display == 'none')
            d.style.display = 'block';
        else
            d.style.display = 'none';
    }
}
/*-----------------------------------------------------------
    Toggles tabs - Closes any open tabs, and then opens current tab
    Input:     1.The number of the current tab
                    2.The number of tabs
                    3.(optional)The number of the tab to leave open
                    4.(optional)Pass in true or false whether or not to animate the open/close of the tabs
    Output: none 
    ---------------------------------------------------------*/
function toggleTab(num,numelems,opennum,animate) {

    if($('step'+num).style.display == 'none'){
        for (var i=1;i<=numelems;i++){

            var tempc = 'step'+i;
            var c = $(tempc);
            if(c.style.display != 'none'){
                if (animate || typeof animate == 'undefined')
                    Effect.toggle(tempc,'appear',{duration:.7, queue:{scope:'menus', limit: 3}});
                else
                    toggleDisp(tempc);
            }
        }
        var c = $('step'+num);
        c.style.marginTop = '2px';
        if (animate || typeof animate == 'undefined') {
            Effect.toggle('step'+num,'appear',{duration:.7, queue:{scope:'menus', position:'end', limit: 3}});
 		}		
		else {
            toggleDisp('step'+num);
        }
    }
}

function toggleBackNext() {
	var themeContent = $("theme-content");
	var toggles = $$("div.toggle");
	for (var i=0;i<toggles.length; i++) {
		toggles[i].style.display = "none";
		toggles[0].style.display = "block";
		
		var newNav = document.createElement("div");
		newNav.setAttribute('class','theme-paginate');
		
		var next = document.createElement("a");
		next.setAttribute('href','#toggle'+(i+2));
		next.setAttribute('class','next');
		next.innerHTML = "Next &#187;";
		
		next.onclick = function(event) {
			var section = this.getAttribute("href").split("#toggle")[1];
			toggleTab(section,toggles.length);
			return false;
		}
		
		var back = document.createElement("a");
		back.setAttribute('href','#toggle'+i);
		back.setAttribute('class','back');
		back.innerHTML = "&#171; Back";
		
		back.onclick = function() {
			var section = this.getAttribute("href").split("#toggle")[1];
			toggleTab(section,toggles.length);
			return false;
		}
		
		if(i!=0) {	
			newNav.appendChild(back);
		} 
		
		if(i!=(toggles.length-1)) {
			newNav.appendChild(next);
		}
		var heading = toggles[i].getElementsByTagName("h3");
		for (var j=0;j<heading.length; j++)
		{
			var head = heading[j];
			insertAfter(newNav,head);
		}
	//	toggles[i].appendChild(newNav);
				
	}

}

function toggleNav() {
	var toggles = $$("div.toggle");
	
	for (var i=0;i<toggles.length; i++) {
		toggles[i].style.display = "none";
		toggles[0].style.display = "block";
	}
	var links = $$("#tertiary-nav a");
	links[0].addClassName('current');
	for (var i=0;i<links.length; i++) {
		var link = links[i];
		var section = link.getAttribute("href").split("#step")[1];
		link.onclick = function() {
			var section = this.getAttribute("href").split("#step")[1];
			this.addClassName('current');
			toggleTab(section,links.length);
			return false;
		}
	}
}

Event.observe(window,'load',toggleNav);


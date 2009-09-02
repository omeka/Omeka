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
    var toggles = $$("div.toggle");
    var togglesLength = toggles.length;
    var links = $$("#tertiary-nav a");
    if(!('tertiary-nav')) return false;
    if(!toggles) return false;
    removeClasses(links);
    for (var i=0;i<toggles.length; i++) {
            
        var newNav = document.createElement("div");
        newNav.setAttribute('class','theme-paginate');
        
        var next = document.createElement("a");
        next.setAttribute('href','#step'+(i+2));
        next.setAttribute('class','next');
        next.innerHTML = "Next &#187;";
        
        next.onclick = function(event) {
            var section = this.getAttribute("href").split("#step")[1];
            toggleTab(section,toggles.length);
            sectionButton = $("stepbutton"+section).getElementsByTagName('a')[0];
            removeClasses(links);
            sectionButton.toggleClassName('current','off');
            return false;
        }
        
        var back = document.createElement("a");
        back.setAttribute('href','#step'+i);
        back.setAttribute('class','back');
        back.innerHTML = "&#171; Back";
        
        back.onclick = function() {
            var section = this.getAttribute("href").split("#step")[1];
            toggleTab(section,toggles.length);
            removeClasses(links);
            sectionButton = $("stepbutton"+section).getElementsByTagName('a')[0];
            removeClasses(links);
            sectionButton.toggleClassName('current','off');
            return false;
        }
        
        if(i!=0) {  
            newNav.appendChild(back);
        } 
        
        if(i!=(toggles.length-1)) {
            newNav.appendChild(next);
            
        }
        
        toggles[i].appendChild(newNav);             
    }
}

function removeClasses(theArray) {
    for(var i=0;i<theArray.length;i++){
    theArray[i].removeClassName('current');
    }
}
function toggleNav() {
    var toggles = $$("div.toggle");
    
    for (var i=0;i<toggles.length; i++) {
        toggles[i].style.display = "none";
        toggles[0].style.display = "block";
    }

    if(!$$("#tertiary-nav a")) return;
    var links = $$("#tertiary-nav a");
    if(!links[0]) return;
    links[0].addClassName('current');
    for (var i=0;i<links.length; i++) {
        var link = links[i];
        link.onclick = function() {
            var section = this.getAttribute("href").split("#step")[1];
            removeClasses(links);
            this.toggleClassName('current','off');
            
            toggleTab(section,links.length);
            return false;
        }
    }
}

/* Event.observe(window,'load',toggleBackNext);
Event.observe(window,'load',toggleNav); */

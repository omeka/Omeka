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
//      new Effect.Highlight(div, {duration:'2.0',startcolor:'#ffff99', endcolor:'#ffffff'});
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
    //  new Effect.Fade(alert,{duration:'6.0'});
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
    
    $$('.delete-element-set').each( function(el) {
        el.onclick = function() {
            return confirm('Are you sure you want to delete this element set? This will delete the element set and all elements assigned to the element set. Items will lose all metadata that is specific to this element set.');
        }
    });
    
    $$('.delete-item').each( function(el) {
        el.onclick = function() {
            return confirm('Are you sure you want to delete this item? This will delete the item and its associated metadata. It will also delete all files and file metadata associated with this item.');
        }
    });
    
    $$('.delete-item-type').each( function(el) {
        el.onclick = function() {
            return confirm('Are you sure you want to delete this item type? This will delete the item type but will not delete the elements assigned to the item type. Items that are assigned to this item type will lose all metadata that is specific to the item type.');
        }
    });
    
    $$('.delete-collection').each( function(el) {
        el.onclick = function() {
            return confirm('Are you sure you want to delete this collection? This will delete the collection and its associated metadata. This will not delete any items in this collection, but will delete the referece to this collection in each item.');
        }
    });
    
    $$('.delete-user').each( function(el) {
        el.onclick = function() {
            return confirm('Are you sure you want to delete this user? This will delete the user from the system, and will prevent the user from logging in unless a new account is created. Items, collections, and tags created by this user will remain in the archive, but will no longer be associated with this user.');
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

Event.observe(window,'load',Omeka.alertBox);
Event.observe(window,'load',confirmDelete);


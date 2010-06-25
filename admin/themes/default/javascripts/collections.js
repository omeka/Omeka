function removeCollector(){
     $$('a.remove-collector').invoke('observe', 'click', function(e){
        e.stop();
        var removeLink = this;
        new Ajax.Request(removeLink.href, {
           parameters: "output=json",
           onComplete: function(t) {
               if(t.responseJSON['result'] == true) {
                   removeLink.up().destroy();
               }
           } 
        });
    }); 
}

Event.observe(window, 'load', removeCollector);
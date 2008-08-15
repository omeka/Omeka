<script type="text/javascript" charset="utf-8">
	
	Event.observe(window, 'load', function(){	  
	    
	    var activateElementForm = function() {
	        // When we submit the form to add an element to an item type, AJAX 
	        // request it instead and branch on the results.
	        // This AJAX request will either return the form partial on failure,
	        // or the form's list of elements on success.  
    	    $('add-element-form').observe('submit', function(e){
    	        e.stop();
    	        this.request({
    	            // Update the element-form partial when this fails (validation errors, etc.)
    	            onFailure: function(t) {
    	                var partial = $('element-form');
    	                partial.update(t.responseText);
    	                new Effect.Highlight(partial);
    	                
    	                activateElementForm();
    	            },
    	            // Update the element-list partial when this succeeds.
    	            onSuccess: function(t) {
    	                var list = $('element-list');
    	                list.update(t.responseText);
    	                // Scroll to the list's header so you can see everything.
    	                $('type-elements').scrollTo();
    	                new Effect.Highlight(list);

    	                activateDeleteElementLinks();
    	            }
    	        });
    	    });
	    };
	    
	    activateElementForm();
	    
	    var activateDeleteElementLinks = function() {
    	    // Turn all the links into AJAX requests that will actually delete the element and reload the list.
    	    $$('a.delete-element').invoke('observe', 'click', function(e){
    	        e.stop();

    	        new Ajax.Request(this.href, {
    	            // Update the text and make sure this responds to ajax requests.
    	            onSuccess: function(t) {
    	                $('element-list').update(t.responseText);
    	                activateDeleteElementLinks();
    	            },
    	            // What to do in this case?  Freak out.
    	            onFailure: function(t) {
    	                alert(t.status);
    	            }
    	        });
    	    });	        
	    };
	    
        activateDeleteElementLinks();
	});
</script>

<style type="text/css" media="screen">
    #element-list div.element-name {float:right;clear:none;}
    #element-list {width: 50%;}
</style>

<?php echo flash(); ?>

<fieldset id="type-information">
	<legend>Item Type Information</legend>
<div class="field">
<?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$itemtype->name, 'Name'); ?>
</div>
<div class="field">
<?php echo label('description', 'Description'); ?>
<?php echo $this->formTextarea('description' ,$itemtype->description, array('class'=>'textinput', 'rows'=>'10')); ?>
</div>
</fieldset>
<fieldset id="type-elements">
	<legend>Elements</legend>
	
    <div id="element-list">
    <?php if($itemtype->exists()): ?>
        <?php echo $this->action('element-list', 'item-types', null, array('item-type-id' => $itemtype->id)); ?>
    <?php endif; ?>
    </div>
</fieldset>
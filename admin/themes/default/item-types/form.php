<script type="text/javascript" charset="utf-8">
    
    Event.observe(window, 'load', function() {

        $('add-element').observe('click', function(e) {
            e.stop();
            var elementCount = $$('#element-list-tbody tr').length;
            var typeValue = Form.getInputs('edit-item-type-form','radio','add-element-type').find(function(radio) { return radio.checked; }).value;
            var addNewRequestUrl = '<?php echo admin_uri('item-types/add-new-element'); ?>';
       
            var addExistingRequestUrl = '<?php echo admin_uri('item-types/add-existing-element'); ?>' + '?elementCount=' + elementCount;
            if (typeValue=='new') {
                requestUrl = addNewRequestUrl;
            } else {
                requestUrl = addExistingRequestUrl; 
            }
            new Ajax.Request(requestUrl, {
    	        method:'get',
    	        parameters: {elementCount: elementCount},
    	        onSuccess: function(transport){
    	          var response = transport.responseText || "no response text";
                  var list = $('element-list-tbody');
                  list.insert({bottom:response});
                  activateRemoveElementLinks();
                  activateSelectElementDropdowns();
            	},
    	        onFailure: function(){ alert('Unable to get a new element.') }
    	      });
        });     

        var activateSelectElementDropdowns = function() {
        	 $$('select.existing-element-drop-down').invoke('observe', 'change', function(e){
                 e.stop();
                 var dropDown = this;
                 var elementId = this.getValue();
                 var addExistingElementIdPrefix = 'add-existing-element-id-';
                 var addExistingElementId = this.getAttribute('id');
                 if (addExistingElementId) {
                     var elementTempId = addExistingElementId.substring(addExistingElementIdPrefix.length);
                	 var requestUrl = '<?php echo admin_uri('item-types/change-existing-element'); ?>';
                     new Ajax.Request(requestUrl, {
                         method:'get',
                         parameters: {elementId: elementId, elementTempId: elementTempId},
                         onSuccess: function(transport){
                           var responseJSON = transport.responseJSON;
                           var elementDescriptionCol = dropDown.up().next();
                           elementDescriptionCol.update(responseJSON.elementDescription);
                           var elementDataTypeNameCol = dropDown.up().next(1);
                           elementDataTypeNameCol.update(responseJSON.elementDataTypeName);
                         },
                         onFailure: function(){ alert('Unable to get selected element data.') }
                       });
                 }
        	 });
        };
        
        var activateRemoveElementLinks = function() {
            // Turn all the links into AJAX requests that will actually delete the element and reload the list.
            $$('a.delete-element').invoke('observe', 'click', function(e){
                e.stop();
                var elementsToRemove = $('elements-to-remove');

                var removeElementLinkPrefix = 'remove-element-link-';
                var removeElementLinkId = this.getAttribute('id');
                if (removeElementLinkId) {    
	                var elementId = removeElementLinkId.substring(removeElementLinkPrefix.length);
	                if (elementId) {
	                    if(!confirm('Are you sure you want to delete this element? This will remove the element from this particular item type. Items that are assigned to this item type will lose metadata that is specific to this element.')) {
	                        return;
	                    }
	                    elementsToRemove.setAttribute('value', elementsToRemove.getAttribute('value') + elementId + ',');
	                }
                }
                var row = this.up().up();
                row.remove();   
            });
        };
        activateRemoveElementLinks();
    });
</script>

<?php echo flash(); ?>

<fieldset id="type-information">
    <legend>Item Type Information <span id="required-note">* Required Fields</span></legend>
    
    <div class="field">
        <?php echo label(array('name'=>'name', 'class' => 'required'),'Name'); ?>
        <div class="inputs">
        <?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$itemtype->name); ?>
        </div>
    </div>
    <div class="field">
    <?php echo label('description', 'Description'); ?>
        <div class="inputs">
        <?php echo $this->formTextarea('description', $itemtype->description, array('class'=>'textinput', 'rows'=>'10', 'cols'=>'40')); ?>
        </div>
    </div>
</fieldset>
<?php if ($itemtype->exists()): ?>

<fieldset id="type-elements">
    <legend>Elements</legend>
    <div id="element-list">
        <?php echo $this->action('element-list', 'item-types', null, array('item-type-id' => $itemtype->id)); ?>
    </div>
</fieldset>
<?php endif; ?>

<?php fire_plugin_hook('admin_append_to_item_types_form', $itemtype); ?>
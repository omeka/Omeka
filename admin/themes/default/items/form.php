<?php echo js('tooltip'); 
echo js('tiny_mce/tiny_mce'); 
// echo js('tiny_mce/tiny_mce_src'); // Use the 'tiny_mce_src' file for debugging.
?>
<?php // The following includes the Autocompleter class. ?>
<?php echo js('scriptaculous', 'javascripts', array('controls'));  ?>

<script type="text/javascript" charset="utf-8">
//<![CDATA[

    Event.observe(window,'load', function() {
        Omeka.ItemForm.enableTagRemoval();
        Omeka.ItemForm.makeFileWindow();
        
        // Reset the IDs of the textareas so as to not confuse the WYSIWYG enabler buttons.
        // $$('div.field textarea').each(function(el){
        //     el.id = null;
        // });

        // Must run the element form scripts AFTER reseting textarea ids.
        document.fire('omeka:elementformload');
        
        Omeka.ItemForm.enableAddFiles();
        Omeka.ItemForm.changeItemType();
    });
    
    document.observe('omeka:elementformload', function(e){
        var elementFieldDiv = e.target;
        Omeka.ItemForm.makeElementControls();
        Omeka.ItemForm.enableWysiwyg();
    });
    
    Omeka.ItemForm = Object.extend(Omeka.ItemForm || {}, {

    makeFileWindow: function() {
        $$('#file-list a').each(function(link) {
            link.onclick = function() {
                window.open(link.getAttribute("href"));
                return false;
            }
        });
    },
    
    changeItemType: function() {
        $('change_type').hide();
        $('item-type').onchange = function() {
            var typeSelectLabel = $$('#type-select label')[0];
            var image = $(document.createElement('img'));
            image.src = <?php echo js_escape(img('loader2.gif')); ?>;
            var params = 'item_id=<?php echo $item->id; ?>&type_id='+this.getValue();
                        
            new Ajax.Request(<?php echo js_escape(uri("items/change-type")) ?>, {
                parameters: params,
                onCreate: function(t) {
                    typeSelectLabel.appendChild(image);
                },
                onFailure: function(t) {
                    alert(t.status);
                },
                onComplete: function(t) {
                    var form = $('type-metadata-form');
                    image.remove();
                    form.update(t.responseText);
                    form.fire('omeka:elementformload');
                    Effect.BlindDown(form);
                }
            });
        }        
    },
    
    addTags: function(tags) {
        var newTags = tags.split(',');
        
        // only add tags from the input box that are new
        var oldTags = $$('#my-tags-list input.remove_tag').map(function(button){
           return button.value.strip();
        });
        
        newTags.each(function(tag){
           var strippedTag = tag.strip();
           if (!oldTags.include(strippedTag)) {
               Omeka.ItemForm.addTagElement(strippedTag);
           }
        });
        
        $('tags').value = '';
    },

    addTag: function(tag) {
        Omeka.ItemForm.addTags(tag);
    },
    
    addTagElement: function(tag) {
        var nRTButton = new Element('li', { 'class': 'tag-delete'});

        var img1 = new Element('input', { 'type': 'image', 'src': <?php echo js_escape(img('add.png')); ?>, 'class': 'undo_remove_tag', 'value': tag});
        nRTButton.appendChild(img1);
        img1.observe('click', function(e) {
            e.stop();
            Omeka.ItemForm.undoRemoveTag(this);
        });
        var img2 = new Element('input', { 'type': 'image', 'src': <?php echo js_escape(img('delete.gif')); ?>, 'class': 'remove_tag', 'value': tag});                    
        img2.observe('click', function(e) {
            e.stop();
            Omeka.ItemForm.removeTag(this);
        });
        nRTButton.appendChild(img2);
        nRTButton.appendChild(document.createTextNode(tag));
        $('my-tags-list').appendChild(nRTButton);
        Omeka.ItemForm.updateTagsField();
        return false;
    },

    removeTag: function(button) {
        button.hide();
        button.parentNode.setOpacity(.3);
        Omeka.ItemForm.updateTagsField();
        return false;
    },
    
    undoRemoveTag: function(button) {
        button.next('input.remove_tag').show();
        button.parentNode.setOpacity(1);
        Omeka.ItemForm.updateTagsField();
        return false;
    },
    
    // update the tags field to only include the tags that have not been removed
    updateTagsField: function() {
        var myTagsToAdd = new Array();
        var myTagsToDelete = new Array();
        if (rTButtons = $$('#my-tags-list input.remove_tag')) {
            rTButtons.each(function(button) {
                // decide whether the toggled tag needs to be included
                var s = button.value.strip();
                if (button.parentNode.getOpacity() == 1) {
                    myTagsToAdd.push(s);
                } else {
                    myTagsToDelete.push(s);
                }
            });         
        }
        
        var otherTagsToDelete = new Array();
        if (rTButtons = $$('#other-tags-list input.remove_tag')) {
            rTButtons.each(function(button) {
                // decide whether a toggled tag needs to be added
                var s = button.value.strip();
                if (button.parentNode.getOpacity() != 1) {
                    otherTagsToDelete.push(s);
                }
            });  
        }
        
        $('my-tags-to-add').value = myTagsToAdd.join(',');
        $('my-tags-to-delete').value = myTagsToDelete.join(',');
        $('other-tags-to-delete').value = otherTagsToDelete.join(',');          
    },
    
    /* Messing with the tag list should not submit the form. */
    enableTagRemoval: function() {      
        if ( !(removeTagButtons = $$('input.remove_tag')) || !(undoRemoveTagButtons = $$('input.undo_remove_tag'))) {
            return;
        }

        $('add-tags-button').observe('click', function(e) {
            e.stop();
            Omeka.ItemForm.addTags($('tags').value);
        });     
        
        removeTagButtons.invoke('observe', 'click', function(e) {
            e.stop();
            Omeka.ItemForm.removeTag(this);
        });
        
        undoRemoveTagButtons.invoke('observe', 'click', function(e) {
            e.stop();
            Omeka.ItemForm.undoRemoveTag(this);
        });
        
        Omeka.ItemForm.updateTagsField();
    },
    
    /**
     * Send an AJAX request to update a <div class="field"> that contains all 
     * the form inputs for an element.
     */
    elementFormRequest: function(fieldDiv, params) {
        var elementId = fieldDiv.id.gsub(/element-/, '');
        
        // Isolate the inputs on this part of the form.
        var inputs = fieldDiv.select('input', 'textarea', 'select');

        var postString = inputs.invoke('serialize').join('&');        
        params.element_id = elementId;

        // note to yourself that this php is hard coded into the javascript
        // you cannot move this javascript to another file even if you wanted to
        var elementFormPartialUri = <?php echo js_escape(uri('items/element-form')); ?>;
        params.item_id = "<?php echo item('id'); ?>";
        
        // Make sure that we put in that we want to add one input to the form
        postString += '&' + $H(params).toQueryString();
        
        new Ajax.Updater(fieldDiv, elementFormPartialUri, {
            parameters: postString,
            onComplete: function(t) {
                fieldDiv.fire('omeka:elementformload');
            }
        });
    },
    
    addElementControl: function(e){
        e.stop();
        var addButton = Event.element(e);
        var fieldDiv = addButton.up('div.field');

        Omeka.ItemForm.elementFormRequest(fieldDiv, {add:'1'});
    },
    
    deleteElementControl: function(e){
        e.stop();
        
        var removeButton = Event.element(e);

        //Check if there is only one input.
        var inputCount = removeButton.up('div.field').select('div.input-block').size();
        if (inputCount == 1) {
            return;
        };

        if(!confirm('Do you want to delete this?')) {
            return;
        }
             
        removeButton.up('div.input-block').remove();
        
        $$('div.field').each(function(i) {
            var removeCount = i.select('.remove-element').size();
            if(removeCount == 1) {
                i.select('.remove-element').each(function(j,index){
                    j.style.display = "none"; 
                }); 
            }
        });
    },
    
    makeElementControls: function() {
        
        $$('div.field').each(function(i) {
            var removeCount = i.select('.remove-element').size();
            if(removeCount > 1) {
                i.select('.remove-element').each(function(j,index){
                    j.style.display = "block"; 
                }); 
            }
        });
        
        // Class name is hard coded here b/c it is hard coded in the helper
        // function as well.
        $$('.add-element').invoke('observe', 'click', Omeka.ItemForm.addElementControl);
        
        // When button is clicked, remove the last input that was added
        $$('.remove-element').invoke('observe', 'click', Omeka.ItemForm.deleteElementControl);
    },
    
    /**
     * Adds an arbitrary number of file input elements to the items form so that
     * more than one file can be uploaded at once.
     */
    enableAddFiles: function() {
        if(!$('add-more-files')) return;
        if(!$('file-inputs')) return;
        if(!$$('#file-inputs .files')) return;
        var nonJsFormDiv = $('add-more-files');

        //This is where we put the new file inputs
        var filesDiv = $$('#file-inputs .files').first();

        var filesDivWrap = $('file-inputs');
        //Make a link that will add another file input to the page
        var link = $(document.createElement('a')).writeAttribute(
            {href:'#',id: 'add-file', className: 'add-file'}).update('Add Another File');

        Event.observe(link, 'click', function(e){
            e.stop();
            var inputs = $A(filesDiv.getElementsByTagName('input'));
            var inputCount = inputs.length;
            var fileHtml = '<div id="fileinput'+inputCount+'" class="fileinput"><input name="file['+inputCount+']" id="file['+inputCount+']" type="file" class="fileinput" /></div>';
            new Insertion.After(inputs.last(), fileHtml);
            $('fileinput'+inputCount).hide();
            new Effect.SlideDown('fileinput'+inputCount,{duration:0.2});
            //new Effect.Highlight('file['+inputCount+']');
        });

        nonJsFormDiv.update();
        
        filesDivWrap.appendChild(link);
    },
    
    enableWysiwygCheckbox: function(checkbox) {
        
        function getTextarea(checkbox) {
            var textarea = checkbox.up('.input-block').down('textarea', 0);
            // We can't use the editor for any field that isn't a textarea
            if (Object.isUndefined(textarea)) {
                return;
            };
            
            textarea.identify();
            return textarea;
        }
        
        // Add the 'html-editor' class to all textareas that are flagged as HTML.
        var textarea = getTextarea(checkbox);
        
        if (checkbox.checked && textarea) {
            textarea.addClassName('html-editor');
        };


        // Whenever the checkbox is toggled, toggle the WYSIWYG editor.
        Event.observe(checkbox, 'click', function(e) {
            var textarea = getTextarea(checkbox);
            
            if (textarea) {
                // Toggle on when checked.
                if(checkbox.checked) {
                    tinyMCE.execCommand("mceAddControl", false, textarea.id);               
                } else {
                    tinyMCE.execCommand("mceRemoveControl", false, textarea.id);
                }                
            };
        });
    },
    
    /**
     * Make it so that checking a box will enable the WYSIWYG HTML editor for
     * any given element field. This can be enabled on a per-textarea basis as
     * opposed to all fields for the element.
     */
    enableWysiwyg: function() {
                
        $$('div.inputs').each(function(div){
            // Get all the WYSIWYG checkboxes
            var checkboxes = div.select('input[type="checkbox"]');
            checkboxes.each(Omeka.ItemForm.enableWysiwygCheckbox);
        });
    
        tinyMCE.init({
            mode: "specific_textareas",
            editor_selector : "html-editor",    // Put the editor in for all textareas with an 'html-editor' class.
            theme: "advanced",
            force_br_newlines : true,
            forced_root_block : '', // Needed for 3.x
            remove_linebreaks : true,
            fix_content_duplication : false,
            fix_list_elements : true,
            valid_child_elements:"ul[li],ol[li]",
            theme_advanced_toolbar_location : "top",
            theme_advanced_buttons1 : "bold,italic,underline,justifyleft,justifycenter,justifyright,bullist,numlist,link,formatselect,code",
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : "",
            theme_advanced_toolbar_align : "left"
       });   
    }

    });
    
    // Tags autocomplete
    Event.observe(window, 'load', function(){
        new Ajax.Autocompleter("tags", "tag-choices", 
        <?php echo js_escape(uri(array('controller'=>'tags', 'action'=>'autocomplete'), 'default', array(), true)); ?>, {
            tokens: ',',
            paramName: 'tag_start'
        });
    });
//]]>   
</script>

<?php echo flash(); ?>

<div id="public-featured">
    <?php if ( has_permission('Items', 'makePublic') ): ?>
        <div class="checkbox">
            <label for="public">Public:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'public', 'id'=>'public'), $item->public); ?></div>
        </div>
    <?php endif; ?>
    <?php if ( has_permission('Items', 'makeFeatured') ): ?>
        <div class="checkbox">
            <label for="featured">Featured:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'featured', 'id'=>'featured'), $item->featured); ?></div>
        </div>
    <?php endif; ?>
</div>
<div id="item-metadata">
<?php foreach ($tabs as $tabName => $tabContent): ?>
    <?php if (!empty($tabContent)): ?>
        <div id="<?php echo text_to_id(html_escape($tabName)); ?>-metadata">
        <fieldset class="set">
            <legend><?php echo html_escape($tabName); ?></legend>
            <?php echo $tabContent; ?>        
        </fieldset>
        </div>     
    <?php endif; ?>
<?php endforeach; ?>
</div>
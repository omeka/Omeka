Omeka.ItemForm = Object.extend(Omeka.ItemForm || {}, {

    makeFileWindow: function() {
        $$('#file-list a').each(function(link) {
            link.onclick = function() {
                window.open(link.getAttribute("href"));
                return false;
            }
        });
    },

    changeItemType: function(changeItemTypeUrl, itemId) {
        $('change_type').hide();
        $('item-type').onchange = function() {
            var typeSelectLabel = $$('#type-select label')[0];
            var params = 'type_id='+this.getValue();
            if (itemId) {
                params += '&item_id='+itemId;
            }
            new Ajax.Request(changeItemTypeUrl, {
                parameters: params,
                onCreate: function(t) {
                },
                onFailure: function(t) {
                    alert(t.status);
                },
                onComplete: function(t) {
                    var form = $('type-metadata-form');
                    form.update(t.responseText);
                    form.fire('omeka:elementformload');
                    Effect.BlindDown(form);
                }
            });
        }        
    },

    addTags: function(tags, addImage, deleteImage) {
        var newTags = tags.split(',');
    
        // only add tags from the input box that are new
        var oldTags = new Array();
        if ($('my-tags-list')) {
            oldTags = $$('#my-tags-list input.remove_tag').map(function(button){
               return button.value.strip();
            });
        }
    
        newTags.each(function(tag){
           var strippedTag = tag.strip();
           if (strippedTag != "" && !oldTags.include(strippedTag)) {
               Omeka.ItemForm.addTagElement(strippedTag, addImage, deleteImage);
           }
        });
    
        $('tags').value = '';
    },

    addTag: function(tag, addImage, deleteImage) {
        Omeka.ItemForm.addTags(tag, addImage, deleteImage);
    },

    addTagElement: function(tag, addImage, deleteImage) {
        var nRTButton = new Element('li', { 'class': 'tag-delete'});

        var img1 = new Element('input', { 'type': 'image', 'src': addImage, 'class': 'undo_remove_tag', 'value': tag});
        nRTButton.appendChild(img1);
        img1.observe('click', function(e) {
            e.stop();
            Omeka.ItemForm.undoRemoveTag(this);
        });
        var img2 = new Element('input', { 'type': 'image', 'src': deleteImage, 'class': 'remove_tag', 'value': tag});
        img2.observe('click', function(e) {
            e.stop();
            Omeka.ItemForm.removeTag(this);
        });
        nRTButton.appendChild(img2);
        nRTButton.appendChild(document.createTextNode(tag));
    
        Omeka.ItemForm.createMyTagsHeaderAndList();
        $('my-tags-list').appendChild(nRTButton);
        Omeka.ItemForm.updateTagsField();
        return false;
    },

    createMyTagsHeaderAndList: function() {
        if (!$('my-tags-list')) {
            var myTagsHeader = new Element('h3');
            myTagsHeader.appendChild(document.createTextNode('My Tags'));
            $('my-tags').appendChild(myTagsHeader);
            var myTagsUL = new Element('ul', {'id':'my-tags-list'})
            $('my-tags').appendChild(myTagsUL);
        }
    },

    removeTag: function(button) {
        button.hide();
        button.up().setOpacity(.3);
        Omeka.ItemForm.updateTagsField();
        return false;
    },

    undoRemoveTag: function(button) {
        button.next('input.remove_tag').show();
        button.up().setOpacity(1);
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
                if (button.up().getOpacity() == 1) {
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
                if (button.up().getOpacity() != 1) {
                    otherTagsToDelete.push(s);
                }
            });  
        }
    
        $('my-tags-to-add').value = myTagsToAdd.join(',');
        $('my-tags-to-delete').value = myTagsToDelete.join(',');
        $('other-tags-to-delete').value = otherTagsToDelete.join(',');          
    },

    /* Messing with the tag list should not submit the form. */
    enableTagRemoval: function(addImage, deleteImage) {      
        if ( !(removeTagButtons = $$('input.remove_tag')) || !(undoRemoveTagButtons = $$('input.undo_remove_tag'))) {
            return;
        }

        $('add-tags-button').observe('click', function(e) {
            e.stop();
            Omeka.ItemForm.addTags($('tags').value, addImage, deleteImage);
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
    
    tagChoices: function(tagChoicesUrl) {
        new Ajax.Autocompleter("tags", "tag-choices", 
        tagChoicesUrl, {
            tokens: ',',
            paramName: 'tag_start'
        });  
    },

    /**
     * Send an AJAX request to update a <div class="field"> that contains all 
     * the form inputs for an element.
     */
    elementFormRequest: function(fieldDiv, params, elementFormPartialUri, itemId) {
        var elementId = fieldDiv.id.gsub(/element-/, '');
    
        // Isolate the inputs on this part of the form.
        var inputs = fieldDiv.select('input', 'textarea', 'select');

        var postString = inputs.invoke('serialize').join('&');        
        params.element_id = elementId;
        params.item_id = itemId;
        
        // Make sure that we put in that we want to add one input to the form
        postString += '&' + $H(params).toQueryString();
    
        new Ajax.Updater(fieldDiv, elementFormPartialUri, {
            parameters: postString,
            onComplete: function(t) {
                fieldDiv.fire('omeka:elementformload');
            }
        });
    },

    makeElementControls: function(elementFormPartialUrl, itemId) {
    
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
        $$('.add-element').invoke('observe', 'click', function(e) {
            e.stop();
            var addButton = Event.element(e);
            var fieldDiv = addButton.up('div.field');

            Omeka.ItemForm.elementFormRequest(fieldDiv, {add:'1'}, elementFormPartialUrl, itemId);
        });
    
        // When button is clicked, remove the last input that was added
        $$('.remove-element').invoke('observe', 'click', function(e) {
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
        });
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

       Omeka.wysiwyg({
           mode: "specific_textareas",
           editor_selector: "html-editor",
           forced_root_block: ""
       });
    }

});
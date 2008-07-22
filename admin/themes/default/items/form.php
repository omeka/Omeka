<?php echo js('tooltip'); 
echo js('tiny_mce/tiny_mce');
?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[

	Event.observe(window,'load', function() {
        Omeka.ItemForm.enableTagRemoval();
        // //Create tooltips for all spans with class="tooltip"
        Omeka.Form.makeTooltips($$('.tooltip'), "<?php echo img('information.png'); ?>");
        Omeka.ItemForm.makeElementControls();
        
        // Reset the IDs of the textareas so as to not confuse the WYSIWYG enabler buttons.
        $$('div.field textarea').each(function(el){
            el.id = null;
        });
        
        Omeka.ItemForm.enableWysiwyg();
		Omeka.ItemForm.enableAddFiles();
        Omeka.ItemForm.changeItemType();
	});
    
    Omeka.ItemForm = Object.extend(Omeka.ItemForm || {}, {

    changeItemType: function() {
		$('change_type').hide();
		$('item-type').onchange = function() {
			var typeSelectLabel = $$('#type-select label')[0];
			var image = $(document.createElement('img'));
			image.src = "<?php echo img('loader2.gif'); ?>";
			var params = 'item_id=<?php echo $item->id; ?>&type_id='+this.getValue();
						
			new Ajax.Request('<?php echo uri("items/change-type") ?>', {
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
					var spans = form.select('.tooltip');
					Omeka.Form.makeTooltips(spans);
					Omeka.ItemForm.elementControls();
                    Omeka.ItemForm.enableWysiwyg();
					Effect.BlindDown(form);
				}
			});
		}        
    },
	
	/* Messing with the tag list should not submit the form.  Instead it runs 
	an AJAX request to remove tags. */
	enableTagRemoval: function() {		
		if ( !(buttons = $$('#tags-list input')) ) {
		    return;
		}

    	function removeTag(button) {
    		var tagId = parseInt(button.value);
    		var uri = "<?php echo uri('items/edit/'.$item->id); ?>";

    		new Ajax.Request("<?php echo uri('items/edit/'.$item->id); ?>", {
    			parameters: 'remove_tag='+ tagId,
    			method: 'post',
    			onSuccess: function(t) {
    				//Fire the other ajax request to update the page
    				new Ajax.Updater('tag-form', "<?php echo uri('items/tag-form/'); ?>", {
    					parameters: {
    						'id': "<?php echo $item->id; ?>"
    					},
    					onComplete: function() {
    					    Effect.Appear('tag-form', {duration: 1.0});
    						Omeka.ItemForm.enableTagRemoval();
    					}
    				});
    			},
    			onFailure: function(t) {
    				alert(t.status);
    			}
    		});

    		return false;
    	}
		
		buttons.invoke('observe', 'click', function(e) {
		    e.stop();
		    removeTag(this);
		});
	},
	
	makeElementControls: function() {
	    // Class name is hard coded here b/c it is hard coded in the helper
	    // function as well.
	    $$('.add-element').invoke('observe', 'click', function(e){
	        // Stop form submissions
	        e.stop();
            
	        // Get the last input div and copy it.
	        var lastInput = this.up('div.field').select('div.input').last();
	        var newInput = lastInput.cloneNode(true);
	        	        
	        // 1) Empty the new form elements
	        // 2) Put it on the page directly below the existing one
	        var formInputs = newInput.select('textarea, input');
	        
	        formInputs.each(function(input){
	            // Force it to have an ID if it doesn't already
	            input.identify();
	            
	            // Set its name to a proper value so that it saves.
	            // This involves grepping the name for its specific index and incrementing that.
	            // Elements[##][1][text] --> Elements[##][2][text]
	            input.name = input.name.gsub(/(Elements\[\d+\]\[)(\d+)\]/, function(match) {
	                return match[1] + (parseInt(match[2]) + 1) + ']'
	            });
	            
	            // Reset its value
	            input.value = '';
	            
	            // Hidden values for each input field should be set to 0
	            if (input.type == 'hidden') {
	                input.value = '0';
	            };
	            
	            // Enable the wysiwyg checkbox
	            if (input.type == 'checkbox') {
                    Omeka.ItemForm.enableWysiwygCheckbox(input);
	            };
	        });
	        	        	        	                    
	        lastInput.insert({after: newInput});
	    });
	    
	    // When button is clicked, remove the last input that was added
	    $$('.remove-element').invoke('observe', 'click', function(e){
	        e.stop();
	        
	        if(!confirm('Do you want to delete this?')) {
	            return;
	        }
	        
	        // The main div for this element is 2 levels up
	        var elementDiv = this.up().up();
	        
	        //Check if there is more than one element, if so then OK to delete.
	        var inputDivs = elementDiv.select('div.input');
	        if(inputDivs.size() > 1) {
	            inputDivs.last().remove();
	        }
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
	        var textarea = checkbox.previous('textarea', 0);

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
    
	    //WYSIWYG Editor
       tinyMCE.init({
        mode: "specific_textareas",
        editor_selector : "html-editor",    // Put the editor in for all textareas with an 'html-editor' class.
       	theme: "advanced",
       	theme_advanced_toolbar_location : "top",
       	theme_advanced_buttons1 : "bold,italic,underline,justifyleft,justifycenter,justifyright,bullist,numlist,link,formatselect",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_align : "left"
       });   
	}

    });




//]]>	
</script>

<?php echo flash(); ?>
<ul id="tertiary-nav" class="navigation">
	<li id="stepbutton1"><a href="#step1">Step One</a></li>
	<li id="stepbutton2"><a href="#step2">Step Two</a></li>
	<li id="stepbutton3"><a href="#step3">Step Three</a></li>
</ul>
<div class="toggle" id="step1">
	<fieldset>
		<legend>Type Metadata</legend>

			<div class="field" id="type-select">
				<?php
				echo label('item-type', 'Item Type'); 
				echo select_item_type_for_item(array(	
            				'name'	=> 'item_type_id',
            				'id'	=> 'item-type' )); ?>
			<input type="submit" name="change_type" id="change_type" value="Pick this type" />	
			</div>
			<div id="type-metadata-form">
			<?php common('change-type', compact('item'), 'items'); ?>
			</div>
			</fieldset>
			<fieldset>
			<legend>Add Files</legend>
			<div class="field" id="add-more-files">
			<label for="add_num_files">Add Files</label>
				<div class="files">
				<?php $numFiles = $_REQUEST['add_num_files'] or $numFiles = 1; ?>
				<?php 
				echo text(array('name'=>'add_num_files','size'=>2),$numFiles);
				echo submit('Add this many files', 'add_more_files'); 
				?>
				</div>
			</div>
			
			<div class="field" id="file-inputs">
			<!-- MAX_FILE_SIZE must precede the file input field -->
				<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
				<label>Find a File</label>
					
				<?php for($i=0;$i<$numFiles;$i++): ?>
				<div class="files">
					<input name="file[<?php echo $i; ?>]" id="file-<?php echo $i; ?>" type="file" class="fileinput" />			
				</div>
				<?php endfor; ?>
			</div>
			
			<?php fire_plugin_hook('append_to_item_form_upload', $item); ?>
		
		<?php if ( item_has_files() ): ?>
			<div class="label">Edit File Metadata</div>
			<div id="file-list">
			<table>
				<thead>
					<tr>
						<th>File Name</th>
						<th>Delete?</th>
					</tr>
				</thead>
				<tbody>
			<?php foreach( $item->Files as $key => $file ): ?>
				<tr>
					<td class="file-link">
						<a class="edit" href="<?php echo uri('files/edit/'.$file->id); ?>">
			
							
								
								<?php echo h($file->original_filename); ?>
						</a>
					</td>
					<td class="delete-link">
						<?php echo checkbox(array('name'=>'delete_files[]'),false,$file->id); ?>
					</td>	
				</tr>
		
			<?php endforeach; ?>
			</tbody>
			</table>
			</div>
			<?php endif; ?>
			</fieldset>
	</div>
	<div id="step2" class="toggle">
<fieldset id="core-metadata">
	<legend>Dublin Core Metadata</legend>
	<?php echo display_element_set_form_for_item($item, 'Dublin Core'); ?>
	
	<?php echo display_element_set_form_for_item($item, 'Omeka Legacy Item'); ?>
	    
         <!-- The language field is the last field on this form that is not
         accurately represented by the ElementForm class. This needs to be
         overridden by a filter defined on the admin theme to display the
         correct languages on this form element. -->
			<div class="field">
			<label for="language">Language</label>
			<?php 
				echo select(
					array('id'=>'language','name'=>'language'), 
					array(
						'eng'=>'English', 
						'rus'=>'Russian',
						'deu'=>'German',
						'fra'=>'French',
						'spa'=>'Spanish',
						'san'=>'Sanskrit'),
					!empty($item->language) ? $item->language : 'eng'); 
			?>
			<span class="tooltip"><?php echo element_metadata('Language', 'description'); ?></span>
			</div>
			
	</fieldset>
</div>
<div id="step3" class="toggle">
	<fieldset id="collection-metadata">
		<legend>Collection Metadata</legend>
		<div class="field">
		<?php 
		echo label('collection-id', 'Collection');
		echo select_collection(array('name'=>'collection_id', 'id'=>'collection-id'),
			$item->collection_id); ?>
		</div>
	</fieldset>

	<fieldset id="miscellaneous">
		<legend>Miscellaneous</legend>
		
		<?php if ( has_permission('Items', 'makePublic') ): ?>
			<div class="field">
				<div class="label">Item is public:</div> 
				<div class="radio"><?php echo radio(array('name'=>'public', 'id'=>'public'), array('0'=>'No','1'=>'Yes'), $item->public); ?></div>
			</div>
		<?php endif; ?>
		<?php if ( has_permission('Items', 'makeFeatured') ): ?>
			<div class="field">
				<div class="label">Item is featured:</div> 
				<div class="radio"><?php echo radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $item->featured); ?></div>
			</div>
		<?php endif; ?>
	
	</fieldset>
	
	<fieldset>
		<legend>Tagging</legend>
			<p>Separate tags with commas (lorem,ipsum,dolor sit,amet).</p>
			<div id="tag-form">
			<?php common('tag-form', compact('item'), 'items'); ?>
			</div>
	</fieldset>
	<fieldset id="additional-plugin-data">
		<?php fire_plugin_hook('append_to_item_form', $item); ?>
	</fieldset>
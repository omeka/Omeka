<?php echo js('tooltip'); ?>
<script type="text/javascript" charset="utf-8">
	Event.observe(window,'load', function() {
		Omeka.ItemForm.enableTagRemoval();
		//Create tooltips for all spans with class="tooltip"
		Omeka.ItemForm.makeTooltips($$('.tooltip'));
		Omeka.ItemForm.changeItemType();
		Omeka.ItemForm.elementControls();
		filesAdding();
	});

    Omeka.ItemForm = Omeka.ItemForm || {};
        
    Omeka.ItemForm.changeItemType = function() {
        var typeSelect = $('item-type');
		$('change_type').hide();
		typeSelect.onchange = function() {
			var typeSelectLabel = $$('#type-select label')[0];
			var image = document.createElement('img');
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
					Omeka.ItemForm.makeTooltips(spans);
					Effect.BlindDown(form);
				}
			});
		}        
    };
    
    /* Loop through all the spans with class="tooltip" and make them visible 
	as tooltips */
    Omeka.ItemForm.makeTooltips = function(tooltipElements) {
		tooltipElements.each(function(span){
		   //The div that wraps the tooltip and the form element
		   var div = span.up();
		   /* Make a helpful image that will show the tooltip when you hover
		    your mouse over it. */
		   var image = document.createElement('img');
		   image.src = "<?php echo img('information.png'); ?>";
		   image.style.cursor = "help";
		   div.appendChild(image);
		   div.style.paddingLeft = "20px";
		   
		   var tooltip = new Tooltip(image, span, 
		       {default_css:true, zindex:100000});
		   span.addClassName('info-window');
		});        
    }
	
	/* Messing with the tag list should not submit the form.  Instead it runs 
	an AJAX request to remove tags */
	Omeka.ItemForm.enableTagRemoval = function() {		
		if ( !(buttons = $$('#tags-list input')) ) {
		    return;
		}
		
		buttons.invoke('observe', 'click', function(e) {
		    e.stop();
		    Omeka.ItemForm.removeTag(this);
		});
	}
	
	Omeka.ItemForm.elementControls = function() {
	    // Class name is hard coded here b/c it is hard coded in the helper
	    // function as well.
	    $$('.add-element').invoke('observe', 'click', function(e){
	        // Stop form submissions
	        e.stop();
	        
	        // Get the input div and copy it.
	        var input = this.up().previous('.input');
	        var newInput = input.cloneNode(true);
	        
	        // 1) Empty the new form element
	        // 2) Put it on the page directly below the existing one
	        var formElement = newInput.down();
	        formElement.value = '';

	        input.insert({after: newInput});
	    });
	    
	    // When button is clicked, remove the last input that was added
	    $$('.remove-element').invoke('observe', 'click', function(e){
	        e.stop();
	        // The main div for this element is 2 levels up
	        var elementDiv = this.up().up();
	        
	        //Check if there is more than one element, if so then OK to delete.
	        var inputDivs = elementDiv.select('div.input');
	        if(inputDivs.size() > 1) {
	            inputDivs.last().destroy();
	        }
	    });
	};
	
	Omeka.ItemForm.removeTag = function(button) {
		var tagId = button.value;
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
				echo label(array(), 'Item Type'); 
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
				<label for="file[<?php echo $i; ?>]">Find a File</label>
					
				<?php for($i=0;$i<$numFiles;$i++): ?>
				<div class="files">
					<input name="file[<?php echo $i; ?>]" id="file[<?php echo $i; ?>]" type="file" class="fileinput" />			
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
				</li>
		
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
	<?php $coreElementSet = array(
	    'Title',
	    'Subject', 
	    'Description',
	    'Creator',
	    'Additional Creator',
	    'Source',
	    'Publisher',
//	    'Date',
	    'Contributor',
	    'Rights',
	    'Rights Holder',
	    'Relation',
	    'Format',
	    'Spatial Coverage',
//	    'Temporal Coverage',
//	    'Language',
	    'Provenance',
	    'Citation'); 
	    
	    //@todo Move this to the controller.
	    $dublinCoreElements = get_db()->getTable('Element')->findForItemBySet($item, 'Dublin Core Metadata Element Set');

	    foreach ($dublinCoreElements as $key => $element) {
	       echo display_form_input_for_element($element);
	    }
	    ?>
	    
		<div class="field">
			<label for="date_year" id="date">Date <span class="notes">(YYYY-MM-DD)</span></label>
			
			<div class="dates">
			<div class="dateinput">
		<input type="text" class="textinput" name="date_year" id="date_year" size="4" value="<?php echo not_empty_or($_POST['date_year'], get_year($item->date)); ?>">
		<input type="text" class="textinput" name="date_month" id="date_month" size="2" value="<?php echo not_empty_or($_POST['date_month'], get_month($item->date)); ?>" />
		<input type="text" class="textinput" name="date_day" id="date_day" size="2" value="<?php echo not_empty_or($_POST['date_day'], get_day($item->date)); ?>">
		
			</div>
			</div>
			<span class="tooltip" id="date_tooltip"><?php echo element_metadata('Date', 'description'); ?></span>
		</div>
		
		<div class="field">
			<label id="temporal-coverage">Temporal Coverage <span class="notes">(YYYY-MM-DD)</span></label>
			<div class="dates">
				<span>From</span>
				<span class="dateinput">
					<input type="text" class="textinput" name="coverage_start_year" id="date_year" size="4" value="<?php echo not_empty_or($_POST['coverage_start_year'], get_year($item->temporal_coverage_start)); ?>"> 
					<input type="text" class="textinput" name="coverage_start_month" id="date_month" size="2" value="<?php echo not_empty_or($_POST['coverage_start_month'], get_month($item->temporal_coverage_start)); ?>" /> 
					<input type="text" class="textinput" name="coverage_start_day" id="date_day" size="2" value="<?php echo not_empty_or($_POST['coverage_start_day'], get_day($item->temporal_coverage_start)); ?>">
				</span>
				<span>to</span>
				<span class="dateinput">
					<input type="text" class="textinput" name="coverage_end_year" id="date_year" size="4" value="<?php echo not_empty_or($_POST['coverage_end_year'], get_year($item->temporal_coverage_end)); ?>"> 
					<input type="text" class="textinput" name="coverage_end_month" id="date_month" size="2" value="<?php echo not_empty_or($_POST['coverage_end_month'], get_month($item->temporal_coverage_end)); ?>" /> 
					<input type="text" class="textinput" name="coverage_end_day" id="date_day" size="2" value="<?php echo not_empty_or($_POST['coverage_end_day'], get_day($item->temporal_coverage_end)); ?>">
				</span>
			</div>
			<span class="tooltip" id="temporal_coverage_tooltip"><?php echo element_metadata('Temporal Coverage', 'description'); ?></span>
		</div>

			<div class="field">
			<label id="language">Language</label>
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
			<span class="tooltip" id="language_tooltip"><?php echo element_metadata('Language', 'description'); ?></span>
			</div>
			
	</fieldset>
</div>
<div id="step3" class="toggle">
	<fieldset id="collection-metadata">
		<legend>Collection Metadata</legend>
		<div class="field">
		<?php 
		echo label(array(), 'Collection');
		echo select_collection(array('name'=>'collection_id'),
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
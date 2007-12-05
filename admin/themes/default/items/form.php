<?php js('tooltip'); ?>
<script type="text/javascript" charset="utf-8">
	Event.observe(window,'load', function() {
		ajaxifyTagRemoval();
		makeTooltips();
		changeTypeMetadata();
		filesAdding();
	});
	
	//Update the type metadata every time a different Item Type is selected	
	function changeTypeMetadata()
	{
		var typeSelect = $('type');
		$('change_type').hide();
		typeSelect.onchange = function() {
			
			var typeSelectLabel = $$('#type-select label')[0];
			var image = document.createElement('img');
			image.src = "<?php echo img('loader2.gif'); ?>";
			var params = 'item_id=<?php echo $item->id; ?>&type_id='+this.getValue();
						
			new Ajax.Request('<?php echo uri("items/changeType") ?>', {
				parameters: params,
				onCreate: function(t) {
					typeSelectLabel.appendChild(image);
				},
				onFailure: function(t) {
					alert(t.status);
					image.remove();
				},
				onComplete: function(t) {
					var form = $('type-metadata-form');
					image.remove();
					form.update(t.responseText);
					Effect.BlindDown(form);
				}
			});
		}
	}
	
	function makeTooltips()
	{
		//Now load the tooltip js
			var tooltipIds = ['title', 'publisher', 'relation', 'language', 'spatial_coverage', 'rights', 'rights_holder', 'description', 'source', 'subject', 'creator', 'additional_creator', 'provenance', 'contributor', 'citation', 'temporal_coverage', 'date', 'format'];
			
		for (var i=0; i < tooltipIds.length; i++) {
			var elId = tooltipIds[i];
			$(elId).style.cursor = "help";
			var image = document.createElement('img');
			image.src = "<?php echo img('information.png'); ?>";
			$(elId).appendChild(image);
			$(elId).style.paddingLeft = "20px";
			var tooltipId = elId + '_tooltip';
			var tooltip = new Tooltip(image, tooltipId, {default_css:true, zindex:100000});
			$(tooltipId).addClassName('info-window');
		};
	}
	
	//Messing with the tag list should not submit the form.  Instead it runs an AJAX request to remove tags
	function ajaxifyTagRemoval()
	{
		if(!$('tags-list')) return;
		var buttons = $('tags-list').getElementsByTagName('input');
		
		for (var i=0; i < buttons.length; i++) {
			buttons[i].onsubmit = function() {
				return false;
			}
			buttons[i].onclick = function() {
				removeTag(this);
				return false;
			};
		};		
	}
	
	function removeTag(button)
	{
		var tagId = button.value;
		var uri = "<?php echo uri('items/edit/'.$item->id); ?>";

		new Ajax.Request("<?php echo uri('items/edit/'.$item->id); ?>", {
			parameters: 'remove_tag='+ tagId,
			method: 'post',
			onSuccess: function(t) {
				//Fire the other ajax request to update the page
				new Ajax.Request("<?php echo uri('items/_tags_remove/'); ?>", {
					parameters: {
						'id': "<?php echo $item->id; ?>"
					},
					onSuccess: function(t) {
						$('tags-list').hide();
						$('tags-list').update(t.responseText);
						Effect.Appear('tags-list', {duration: 1.0});
					},
					onComplete: function() {
						ajaxifyTagRemoval();
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
				<?php select(	array(	
							'name'	=> 'type_id',
							'id'	=> 'type' ),
							types(),
							$item->type_id,
							'Item Type',
							'id',
							'name' ); ?>
			<input type="submit" name="change_type" id="change_type" value="Pick this type" />	
			</div>
			<div id="type-metadata-form">
			<?php common('_type', compact('item'), 'items'); ?>
			</div>
			</fieldset>
			<fieldset>
			<legend>Add Files</legend>
			<div class="field" id="add-more-files">
			<label for="add_num_files">Add Files</label>
				<div class="files">
				<?php $numFiles = $_REQUEST['add_num_files'] or $numFiles = 1; ?>
				<?php 
				text(array('name'=>'add_num_files','size'=>2),$numFiles);
				submit('Add this many files', 'add_more_files'); 
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
		
		<?php if ( has_files($item) ): ?>
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
						<?php //if ($file->hasThumbnail() ): ?>
							<?php //thumbnail($file,array(),50,50); ?>
						<?php// endif; ?>
						<a class="edit" href="<?php echo uri('files/edit/'.$file->id); ?>">
			
							
								
								<?php echo h($file->original_filename); ?>
						</a>
					</td>
					<td class="delete-link">
						<?php checkbox(array('name'=>'delete_files[]'),false,$file->id); ?>
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
	<legend>Core Metadata</legend>
		<div class="field">
		<label for="title" id="title">Title</label>
		<input type="text" class="textinput" name="title" value="<?php echo h($item->title);?>" />
		<span class="tooltip" id="title_tooltip"><?php dublin_core('title'); ?></span>
		</div>

		<div class="field">
		<label id="subject">Subject</label>
		<input type="text" class="textinput" name="subject" value="<?php echo h($item->subject);?>" />
		<span class="tooltip" id="subject_tooltip"><?php dublin_core('subject'); ?></span>
		</div>
		
		<div class="field">
		<label id="description">Description</label>
		<textarea class="textinput" name="description"  rows="15" cols="50"><?php echo h($item->description); ?></textarea>
		<span class="tooltip" id="description_tooltip"><?php dublin_core('description'); ?></span>
		</div>
		
		<div class="field">
		<label id="creator">Creator</label>
		<input type="text" class="textinput" name="creator" value="<?php echo h($item->creator);?>" />
		<span class="tooltip" id="creator_tooltip"><?php dublin_core('creator'); ?></span>
		</div>

		<div class="field">
		<label id="additional_creator">Additional Creator</label>
		<input type="text" class="textinput" name="additional_creator" value="<?php echo h($item->additional_creator);?>" />
		<span class="tooltip" id="additional_creator_tooltip"><?php dublin_core('additional_creator'); ?></span>
		</div>
		
		<div class="field">
		<label id="source">Source</label>
		<input type="text" class="textinput" name="source" value="<?php echo h($item->source);?>" />
		<span class="tooltip" id="source_tooltip"><?php dublin_core('source'); ?></span>
		</div>
		
		<div class="field">
		<label id="publisher">Publisher</label>
		<input type="text" class="textinput" name="publisher" value="<?php echo h($item->publisher);?>" />
		<span class="tooltip" id="publisher_tooltip"><?php dublin_core('publisher'); ?></span>
		</div>
		
		<div class="field">
			<label for="date_year" id="date">Date <span class="notes">(YYYY-MM-DD)</span></label>
			
			<div class="dates">
			<div class="dateinput">
		<input type="text" class="textinput" name="date_year" id="date_year" size="4" value="<?php echo not_empty_or($_POST['date_year'], get_year($item->date)); ?>">
		<input type="text" class="textinput" name="date_month" id="date_month" size="2" value="<?php echo not_empty_or($_POST['date_month'], get_month($item->date)); ?>" />
		<input type="text" class="textinput" name="date_day" id="date_day" size="2" value="<?php echo not_empty_or($_POST['date_day'], get_day($item->date)); ?>">
		
			</div>
			</div>
			<span class="tooltip" id="date_tooltip"><?php dublin_core('date'); ?></span>
		</div>
		
		<div class="field">
		<label id="contributor">Contributor</label>
		<input type="text" class="textinput" name="contributor" value="<?php echo h($item->contributor);?>" />
		<span class="tooltip" id="contributor_tooltip"><?php dublin_core('contributor'); ?></span>
		</div>
		
		<div class="field">
		<label id="rights">Rights</label>
		<input type="text" class="textinput" name="rights" value="<?php echo h($item->rights);?>" />
		<span class="tooltip" id="rights_tooltip"><?php dublin_core('rights'); ?></span>
		</div>
		
		<div class="field">
		<label id="rights_holder">Rights Holder</label>
		<input type="text" class="textinput" name="rights_holder" value="<?php echo h($item->rights_holder);?>" />
		<span class="tooltip" id="rights_holder_tooltip"><?php dublin_core('rights_holder'); ?></span>
		</div>
		
		<div class="field">
		<label id="relation">Relation</label>
		<input type="text" class="textinput" name="relation" value="<?php echo h($item->relation);?>" />
		<span class="tooltip" id="relation_tooltip"><?php dublin_core('relation'); ?></span>
		</div>
		
		<div class="field">
		<label id="format">Format</label>
		<input type="text" class="textinput" name="format" value="<?php echo h($item->format);?>" />
		<span class="tooltip" id="format_tooltip"><?php dublin_core('format'); ?></span>
		</div>
		
		<div class="field">
		<label id="spatial_coverage">Spatial Coverage</label>
		<input type="text" class="textinput" name="spatial_coverage" value="<?php echo h($item->spatial_coverage);?>" />
		<span class="tooltip" id="spatial_coverage_tooltip"><?php dublin_core('spatial_coverage'); ?></span>
		</div>
		
		<div class="field">
			<label id="temporal_coverage">Temporal Coverage <span class="notes">(YYYY-MM-DD)</span></label>
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
			<span class="tooltip" id="temporal_coverage_tooltip"><?php dublin_core('temporal_coverage'); ?></span>
		</div>

			<div class="field">
			<label id="language">Language</label>
			<?php 
				select(
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
			<span class="tooltip" id="language_tooltip"><?php dublin_core('language'); ?></span>
			</div>

			<div class="field">
			<label id="provenance">Provenance</label>
			<input type="text" class="textinput" name="provenance" value="<?php echo h($item->provenance);?>" />
			<span class="tooltip" id="provenance_tooltip"><?php dublin_core('provenance'); ?></span>
			</div>

			<div class="field">
			<label id="citation">Bibliographic Citation</label>
			<input type="text" class="textinput" name="citation" value="<?php echo h($item->citation);?>" />
			<span class="tooltip" id="citation_tooltip"><?php dublin_core('bibliographic_citation'); ?></span>
			</div>
	</fieldset>
</div>
<div id="step3" class="toggle">
	<fieldset id="collection-metadata">
		<legend>Collection Metadata</legend>
		<div class="field">
		<?php select('collection_id',
					collections(),
					$item->collection_id,
					'Collection',
					'id',
					'name' ); ?>
		</div>
	</fieldset>

	<fieldset id="miscellaneous">
		<legend>Miscellaneous</legend>
		
		<?php if ( has_permission('Items', 'makePublic') ): ?>
			<div class="field">
				<div class="label">Item is public:</div> 
				<div class="radio"><?php radio(array('name'=>'public', 'id'=>'public'), array('0'=>'No','1'=>'Yes'), $item->public); ?></div>
			</div>
		<?php endif; ?>
		<?php if ( has_permission('Items', 'makeFeatured') ): ?>
			<div class="field">
				<div class="label">Item is featured:</div> 
				<div class="radio"><?php radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $item->featured); ?></div>
			</div>
		<?php endif; ?>
	
	</fieldset>
	
	<fieldset>
		<legend>Tagging</legend>
			<p>Separate tags with commas (lorem,ipsum,dolor sit,amet).</p>
			<div class="field">
			<label for="tags-field">Your Tags</label>
			<input type="text" name="tags" id="tags-field" class="textinput" value="<?php echo not_empty_or($_POST['tags'], tag_string(current_user_tags($item))); ?>" />
			</div>
		
			<?php fire_plugin_hook('append_to_item_form_tags', $item); ?>
			
			<?php if(has_tags($item) and has_permission('Items','untagOthers')): ?>
			<div class="field">
				<label for="all-tags">Remove Other Users' Tags</label>
				<ul id="tags-list">
					<?php common('_tags_remove', compact('item'), 'items'); ?>
				</ul>
			</div>
			<?php endif; ?>
			
	</fieldset>
	<fieldset id="additional-plugin-data">
		<?php fire_plugin_hook('append_to_item_form', $item); ?>
	</fieldset>
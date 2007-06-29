<?php js('tooltip'); ?>
<script type="text/javascript" charset="utf-8">
	Event.observe(window,'load', function() {
		ajaxifyTagRemoval();
		makeTooltips();
		ajaxifyTypeMetadata();
		filesAdding();
	});
	
	function filesAdding()
	{
		var nonJsFormDiv = $('add-more-files');
		
		//This is where we put the new file inputs
		var filesDiv = $$('#file-inputs .files')[0];
		
		//Make a link that will add another file input to the page
		var link = Builder.node('a', {href:'javascript:void(0);'}, 'Add another file');

		Event.observe(link, 'click', function(){
			var inputs = $A(filesDiv.getElementsByTagName('input'));
			var inputCount = inputs.length;
			var fileHtml = '<input name="file['+inputCount+']" id="file['+inputCount+']" type="file" class="fileinput" />';
			new Insertion.After(inputs.last(), fileHtml);
		});
	
		
		nonJsFormDiv.update();
		nonJsFormDiv.appendChild(link);
	}
		
	function ajaxifyTypeMetadata()
	{
		var typeSelect = $('type');
		
		typeSelect.onchange = function() {
			new Ajax.Request('<?php echo uri("items/ajaxTypeMetadata") ?>', {
				parameters: 'id=<?php echo $item->id; ?>&type_id='+this.getValue(),
				onFailure: function(t) {
					alert(t.status);
				},
				onComplete: function(t) {
					var form = $('type-metadata-form');
					form.hide();
					form.update(t.responseText);
					Effect.BlindDown(form, {duration: 1.0});
				}
			});
		}
	}
	
	function makeTooltips()
	{
		//Now load the tooltip js
			var tooltipIds = ['title', 'publisher', 'relation', 'language', 'spatial_coverage', 'rights', 'rights_holder', 'description', 'source', 'subject', 'creator', 'additional_creator', 'provenance', 'contributor', 'citation', 'temporal_coverage', 'date'];
			
		for (var i=0; i < tooltipIds.length; i++) {
			var elId = tooltipIds[i];
			var tooltipId = elId + '_tooltip';
			var tooltip = new Tooltip(elId, tooltipId, {default_css:true, zindex:100000});
		};
	}
	
	function ajaxifyTagRemoval()
	{
		var buttons = $('tags-list').getElementsByTagName('button');
		for (var i=0; i < buttons.length; i++) {
			buttons[i].onsubmit = function() {
				return false;
			}
		
			buttons[i].onclick = function(e) {
				removeTag(e.target);
				return false;
			};
		};		
	}
	
	function removeTag(button)
	{
		var tagId = button.value;
		var uri = "<?php echo uri('items/edit/'.$item->id); ?>";
		
		new Ajax.Request("<?php echo uri('items/edit/'.$item->id); ?>", {
			parameters: {
				'remove_tag': tagId
			},
			method: 'post',
			onComplete: function(t) {
				//Fire the other ajax request to update the page
				new Ajax.Request("<?php echo uri('items/ajaxTagsRemove/'); ?>", {
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
			}
		});
		
		return false;
	}
</script>

<?php echo flash(); ?>

<fieldset id="core-metadata">
	<legend>Core Metadata</legend>
		<div class="field">
		<label for="title" id="title">Title</label>
		<span class="tooltip" id="title_tooltip"><?php dublin_core('title'); ?></span>
		<input type="text" class="textinput" name="title" value="<?php echo $item->title;?>" />
		</div>
		
		<div class="field"><label id="publisher">Publisher</label>
		<span class="tooltip" id="publisher_tooltip"><?php dublin_core('publisher'); ?></span>
		<input type="text" class="textinput" name="publisher" value="<?php echo $item->publisher?>" />
		</div>
		
		<div class="field">
		<label id="relation">Relation</label>
		<span class="tooltip" id="relation_tooltip"><?php dublin_core('relation'); ?></span>
		<input type="text" class="textinput" name="relation" value="<?php echo $item->relation;?>" />
		</div>
		
		<div class="field">
		<label id="language">Language</label>
		<span class="tooltip" id="language_tooltip"><?php dublin_core('language'); ?></span>
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
		
		</div>
		
		<div class="field">
		<label id="spatial_coverage">Spatial Coverage</label>
		<span class="tooltip" id="spatial_coverage_tooltip"><?php dublin_core('spatial_coverage'); ?></span>
		<input type="text" class="textinput" name="spatial_coverage" value="<?php echo $item->spatial_coverage;?>" />
		</div>
		
		<div class="field">
		<label id="rights">Rights</label>
		<span class="tooltip" id="rights_tooltip"><?php dublin_core('rights'); ?></span>
		<input type="text" class="textinput" name="rights" value="<?php echo $item->rights;?>" />
		</div>
		
		<div class="field">
		<label id="rights_holder">Rights Holder</label>
		<span class="tooltip" id="rights_holder_tooltip"><?php dublin_core('rights_holder'); ?></span>
		<input type="text" class="textinput" name="rights_holder" value="<?php echo $item->rights_holder;?>" />
		</div>
		
		<div class="field">
		<label id="description">Description</label>
		<span class="tooltip" id="description_tooltip"><?php dublin_core('description'); ?></span>
		<textarea class="textinput" name="description"  rows="15" cols="50"><?php echo $item->description; ?></textarea>
		</div>
		
		<div class="field">
		<label id="source">Source</label>
		<span class="tooltip" id="source_tooltip"><?php dublin_core('source'); ?></span>
		<input type="text" class="textinput" name="source" value="<?php echo $item->source;?>" />
		</div>
		
		<div class="field">
		<label id="subject">Subject</label>
		<span class="tooltip" id="subject_tooltip"><?php dublin_core('subject'); ?></span>
		<input type="text" class="textinput" name="subject" value="<?php echo $item->subject;?>" />
		</div>
		
		<div class="field">
		<label id="creator">Creator</label>
		<span class="tooltip" id="creator_tooltip"><?php dublin_core('creator'); ?></span>
		<input type="text" class="textinput" name="creator" value="<?php echo $item->creator;?>" />
		</div>
		
		<div class="field">
		<label id="additional_creator">Additional Creator</label>
		<span class="tooltip" id="additional_creator_tooltip"><?php dublin_core('additional_creator'); ?></span>
		<input type="text" class="textinput" name="additional_creator" value="<?php echo $item->additional_creator;?>" />
		</div>
		
		<div class="field">
		<label id="provenance">Provenance</label>
		<span class="tooltip" id="provenance_tooltip"><?php dublin_core('provenance'); ?></span>
		<input type="text" class="textinput" name="provenance" value="<?php echo $item->provenance;?>" />
		</div>
		
		<div class="field">
		<label id="contributor">Contributor</label>
		<span class="tooltip" id="contributor_tooltip"><?php dublin_core('contributor'); ?></span>
		<input type="text" class="textinput" name="contributor" value="<?php echo $item->contributor;?>" />
		</div>
		
		<div class="field">
		<label id="citation">Bibliographic Citation</label>
		<span class="tooltip" id="citation_tooltip"><?php dublin_core('bibliographic_citation'); ?></span>
		<input type="text" class="textinput" name="citation" value="<?php echo $item->citation;?>" />
		</div>
		
		<div class="field">
			<label for="date_year" id="date">Date <span class="notes">(YYYY-MM-DD)</span></label>
			<span class="tooltip" id="date_tooltip"><?php dublin_core('date'); ?></span>
			
			<div class="dates">
			<div class="dateinput">
		<input type="text" class="textinput" name="date_year" id="date_year" size="4" value="<?php echo not_empty_or($_POST['date_year'], get_year($item->date)); ?>">
		<input type="text" class="textinput" name="date_month" id="date_month" size="2" value="<?php echo not_empty_or($_POST['date_month'], get_month($item->date)); ?>" />
		<input type="text" class="textinput" name="date_day" id="date_day" size="2" value="<?php echo not_empty_or($_POST['date_day'], get_day($item->date)); ?>">
		
		</div>
		</div>
		</div>
		
		<div class="field">
			<label id="temporal_coverage">Temporal Coverage <span class="notes">(YYYY-MM-DD)</span></label>
			<span class="tooltip" id="temporal_coverage_tooltip"><?php dublin_core('temporal_coverage'); ?></span>
			<div class="dates">
				<div class="dateinput">
					<input type="text" class="textinput" name="coverage_start_year" id="date_year" size="4" value="<?php echo not_empty_or($_POST['coverage_start_year'], get_year($item->temporal_coverage_start)); ?>"> 
					<input type="text" class="textinput" name="coverage_start_month" id="date_month" size="2" value="<?php echo not_empty_or($_POST['coverage_start_month'], get_month($item->temporal_coverage_start)); ?>" /> 
					<input type="text" class="textinput" name="coverage_start_day" id="date_day" size="2" value="<?php echo not_empty_or($_POST['coverage_start_day'], get_day($item->temporal_coverage_start)); ?>">
				</div>
				
				<div class="dateinput">
					<input type="text" class="textinput" name="coverage_end_year" id="date_year" size="4" value="<?php echo not_empty_or($_POST['coverage_end_year'], get_year($item->temporal_coverage_end)); ?>"> 
					<input type="text" class="textinput" name="coverage_end_month" id="date_month" size="2" value="<?php echo not_empty_or($_POST['coverage_end_month'], get_month($item->temporal_coverage_end)); ?>" /> 
					<input type="text" class="textinput" name="coverage_end_day" id="date_day" size="2" value="<?php echo not_empty_or($_POST['coverage_end_day'], get_day($item->temporal_coverage_end)); ?>">
				</div>
			</div>
		</div>
		
	</fieldset>
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
	
	<fieldset id="type-metadata">
		<legend>Type Metadata</legend>
		<div class="field">
			<?php select(	array(	
						'name'	=> 'type_id',
						'id'	=> 'type' ),
						types(),
						$item->type_id,
						'Item Type',
						'id',
						'name' ); ?>
		<input type="submit" name="change_type" value="Pick this type" />	
		</div>
		<div id="type-metadata-form">
		<?php metatext_form($item,'textarea'); ?>
		</div>

		</fieldset>
		<fieldset id="add-files">
			<legend>Add Files</legend>
			<div class="field" id="add-more-files">
				<label for="add_num_files">Add Files</label>
				<div class="files">
			<?php 
				$numFiles = $_REQUEST['add_num_files'] or $numFiles = 1; 
			?>
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
		</fieldset>
		
		<?php if ( has_files($item) ): ?>
			<fieldset id="files">
			<legend>Edit existing files</legend>
			<p>(click on file to edit on a new page, check 'Delete this' to remove files)</p>
			<ul id="file-list">
			<?php foreach( $item->Files as $key => $file ): ?>
				<li>
					<div class="file-link">
						<a href="<?php echo uri('files/edit/'.$file->id); ?>" target="_blank">
			
							<?php if ( !$file->hasThumbnail() ): ?>
								<?php echo $file->original_filename; ?>
							<?php else: ?>
								<?php thumbnail($file); ?>
							<?php endif; ?>
						</a>
					</div>
					<div class="delete-link">
						<?php checkbox(array('name'=>'delete_files[]'),false,$file->id,'Delete this file'); ?>
					</div>	
				</li>
		
			<?php endforeach; ?>
			</ul>
			</fieldset>
		<?php endif; ?>
		
		<fieldset id="miscellaneous">
			<legend>Miscellaneous</legend>
			<?php if ( has_permission('Items', 'makePublic') ): ?>
				<div class="field">
	<div class="label">Item is public:</div> <div class="radio"><?php radio(array('name'=>'public', 'id'=>'public'), array('0'=>'No','1'=>'Yes'), $item->public); ?></div>
				</div>
			<?php endif; ?>
		<?php if ( has_permission('Items', 'makeFeatured') ): ?>
			<div class="field">
		<div class="label">Item is featured:</div> <div class="radio"><?php radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $item->featured); ?></div>
			</div>
		<?php endif; ?>
	</fieldset>
	
	<fieldset>
		<legend>Tagging</legend>
		<div class="field">
		<label for="tags-field">Modify Your Tags</label>
		<input type="text" name="tags" id="tags-field" class="textInput" value="<?php echo not_empty_or($_POST['tags'], tag_string(current_user_tags($item))); ?>" />
		</div>
		
		<div class="field">
			<label for="all-tags">Remove Other Users&apos; Tags</label>
			<ul id="tags-list">
			<?php foreach( $item->Tags as $key => $tag ): ?>
				<li>
					<?php echo $tag->name; ?>
					<button type="submit" name="remove_tag" value="<?php echo $tag->id; ?>">[x]</button>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		
	</fieldset>
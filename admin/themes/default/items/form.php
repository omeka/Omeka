<script type="text/javascript" charset="utf-8">
	Event.observe(window,'load', function() {
		ajaxifyTagRemoval();
	});
	
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

<?php error($item); ?>
<fieldset id="core-metadata">
	<legend>Core Metadata</legend>
		<div class="field">
		<label>Title</label>
		<input type="text" class="textinput" name="title" value="<?php echo $item->title;?>" />
		<?php error($item, 'title'); ?>
		</div>
		
		<div class="field"><label>Publisher</label>
		<input type="text" class="textinput" name="publisher" value="<?php echo $item->publisher?>" />
		</div>
		
		<div class="field">
		<label>Relation</label>
		<input type="text" class="textinput" name="relation" value="<?php echo $item->relation;?>" />
		</div>
		
		<div class="field">
		<label>Language</label>
		<input type="text" class="textinput" name="language" value="<?php echo $item->language;?>" />
		</div>
		
		<div class="field">
		<label>Spatial Coverage</label>
		<input type="text" class="textinput" name="spatial_coverage" value="<?php echo $item->spatial_coverage;?>" />
		</div>
		
		<div class="field">
		<label>Rights</label>
		<input type="text" class="textinput" name="rights" value="<?php echo $item->rights;?>" />
		</div>
		
		<div class="field">
		<label>Rights Holder</label>
		<input type="text" class="textinput" name="rights_holder" value="<?php echo $item->rights_holder;?>" />
		</div>
		
		<div class="field">
		<label>Description</label>
		<textarea class="textinput" name="description"  rows="15" cols="50"><?php echo $item->description; ?></textarea>
		</div>
		
		<div class="field">
		<label>Source</label>
		<input type="text" class="textinput" name="source" value="<?php echo $item->source;?>" />
		</div>
		
		<div class="field">
		<label>Subject</label>
		<input type="text" class="textinput" name="subject" value="<?php echo $item->subject;?>" />
		</div>
		
		<div class="field">
		<label>Creator</label>
		<input type="text" class="textinput" name="creator" value="<?php echo $item->creator;?>" />
		</div>
		
		<div class="field">
		<label>Additional Creator</label>
		<input type="text" class="textinput" name="additional_creator" value="<?php echo $item->additional_creator;?>" />
		</div>
		
		<div class="field">
		<label>Provenance</label>
		<input type="text" class="textinput" name="provenance" value="<?php echo $item->provenance;?>" />
		</div>
		
		<div class="field">
		<label>Contributor</label>
		<input type="text" class="textinput" name="contributor" value="<?php echo $item->contributor;?>" />
		</div>
		
		<div class="field">
		<label>Bibliographic Citation</label>
		<input type="text" class="textinput" name="citation" value="<?php echo $item->citation;?>" />
		</div>
		
		<div class="field">
		<label>Date</label>
		<input type="text" class="textinput" name="date_year" id="date_year" size="4" value="<?php echo get_year($item->date); ?>"> -
		<input type="text" class="textinput" name="date_month" id="date_month" size="2" value="<?php echo get_month($item->date); ?>" /> -
		<input type="text" class="textinput" name="date_day" id="date_day" size="2" value="<?php echo get_day($item->date); ?>">
		(YYYY-MM-DD)
		</div>
		
		<div class="field">
		<label>Temporal Coverage</label>
		<input type="text" class="textinput" name="coverage_start_year" id="date_year" size="4" value="<?php echo get_year($item->temporal_coverage_start); ?>"> -
		<input type="text" class="textinput" name="coverage_start_month" id="date_month" size="2" value="<?php echo get_month($item->temporal_coverage_start); ?>" /> -
		<input type="text" class="textinput" name="coverage_start_day" id="date_day" size="2" value="<?php echo get_day($item->temporal_coverage_start); ?>">
		(Start YYYY-MM-DD)
		
		<input type="text" class="textinput" name="coverage_end_year" id="date_year" size="4" value="<?php echo get_year($item->temporal_coverage_end); ?>"> -
		<input type="text" class="textinput" name="coverage_end_month" id="date_month" size="2" value="<?php echo get_month($item->temporal_coverage_end); ?>" /> -
		<input type="text" class="textinput" name="coverage_end_day" id="date_day" size="2" value="<?php echo get_day($item->temporal_coverage_end); ?>">
		(End YYYY-MM-DD)
		</div>
		
	</fieldset>
	<fieldset id="collection-metadata">
		<legend>Collection Metadata</legend>
		<div class="field">
		<?php select(	array(	
					'name'	=> 'collection_id',
					'id'	=> 'collection' ),
					get_collections(),
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
						get_types(),
						$item->type_id,
						'Item Type',
						'id',
						'name' ); ?>
		<input type="submit" name="change_type" value="Pick this type" />	
		</div>
		
		<?php metatext_form($item,'textarea'); ?>
		

		</fieldset>
		<fieldset id="add-files">
			<legend>Add Files</legend>
			<div class="field">
			<?php 
				$numFiles = $_REQUEST['add_num_files'] or $numFiles = 1; 
			?>
				<?php 
					text(array('name'=>'add_num_files','size'=>2),$numFiles);
					submit('Add this many more files', 'add_more_files'); 
				?>
				
			</div>
			
			<div class="field">
			<!-- MAX_FILE_SIZE must precede the file input field -->
				<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
					
			<?php for($i=0;$i<$numFiles;$i++): ?>	
				<label for="file[<?php echo $i; ?>]">Find a File</label>
				<input name="file[<?php echo $i; ?>]" id="file[<?php echo $i; ?>]" type="file" class="textinput" />
			<?php endfor; ?>
			</div>
		</fieldset>
		
		<?php if ( $item->hasFiles() ): ?>
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
			<div class="field">
	<label for="public">Item is public: <?php radio(array('name'=>'public', 'id'=>'public'), array('0'=>'No','1'=>'Yes'), $item->public); ?></label>
		</div>
		<div class="field">
		<label for="featured">Item is featured: <?php radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $item->featured); ?></label>
		</div>
	</fieldset>
	
	<fieldset>
		<legend>Tagging</legend>
		<div class="field">
		<label for="tags-field">Modify Your Tags</label>
		<input type="text" name="tags" id="tags-field" class="textInput" value="<?php echo tag_string(current_user_tags($item)); ?>" />
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
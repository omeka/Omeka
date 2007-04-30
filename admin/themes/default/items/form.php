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
		<label>Coverage</label>
		<input type="text" class="textinput" name="coverage" value="<?php echo $item->coverage;?>" />
		</div>
		
		<div class="field">
		<label>Rights</label>
		<input type="text" class="textinput" name="rights" value="<?php echo $item->rights;?>" />
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
		<label>Date</label>
		<input type="text" class="textinput" name="date" value="<?php echo $item->date;?>" />
		</div>
		
	</fieldset>
	<fieldset id="collection-metadata">
		<legend>Collection Metadata</legend>
		<div class="field">
		<?php select(	array(	
					'name'	=> 'collection_id',
					'id'	=> 'collection' ),
					$collections,
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
						$types,
						$item->type_id,
						'Type Info',
						'id',
						'name' ); ?>
						<input type="submit" name="change_type" value="Pick this type" />
						
					</div>
		
		<?php
			metatext_form($item,'textarea');
		?>
		

		</fieldset>
		<fieldset id="files">
			<legend>Files</legend>
			<div class="field">
			<label for="file[0]">Find a File</label>
			<!-- MAX_FILE_SIZE must precede the file input field -->
			<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
			<input name="file[0]" id="file[0]" type="file" class="textinput" />
			</field>
		</fieldset>
		<fieldset id="miscellaneous">
			<legend>Miscellaneous</legend>
			<div class="field">
	<label for="public">Item is public: <?php checkbox(array('name'=>'public', 'id'=>'public'), $item->public); ?></label>
		
		<label for="featured">Item is featured: <?php checkbox(array('name'=>'featured', 'id'=>'featured'), $item->featured); ?></label>
	</div>
	</fieldset>

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
		<fieldset id="files">
			<legend>Files</legend>
			<div class="field">
				<label for="file[0]">Find a File</label>
				<!-- MAX_FILE_SIZE must precede the file input field -->
				<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
				<input name="file[]" id="file[]" type="file" class="textinput" />
			</div>
		</fieldset>
		<fieldset id="miscellaneous">
			<legend>Miscellaneous</legend>
			<div class="field">
	<label for="public">Item is public: <?php radio(array('name'=>'public', 'id'=>'public'), array('0'=>'No','1'=>'Yes'), $item->public); ?></label>
		</div>
		<div class="field">
		<label for="featured">Item is featured: <?php radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $item->featured); ?></label>
	</div>
	</fieldset>
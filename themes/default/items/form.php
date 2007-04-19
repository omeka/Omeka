<?php error($item); ?>
<fieldset id="core-metadata">
	<legend>Core Metadata</legend>
		<label>Title</label>
		<input type="text" class="textinput" name="title" value="<?php echo $item->title;?>" />
		<?php error($item, 'title'); ?>
		
		<label>Publisher</label>
		<input type="text" class="textinput" name="publisher" value="<?php echo $item->publisher?>" />
		
		<label>Relation</label>
		<input type="text" class="textinput" name="relation" value="<?php echo $item->relation;?>" />
		
		<label>Language</label>
		<input type="text" class="textinput" name="language" value="<?php echo $item->language;?>" />

		<label>Coverage</label>
		<input type="text" class="textinput" name="coverage" value="<?php echo $item->coverage;?>" />
		
		<label>Rights</label>
		<input type="text" class="textinput" name="rights" value="<?php echo $item->rights;?>" />
		
		<label>Description</label>
		<textarea class="textinput" name="description"  rows="15" cols="50"><?php echo $item->description; ?></textarea>
		
		<label>Source</label>
		<input type="text" class="textinput" name="source" value="<?php echo $item->source;?>" />
		
		<label>Subject</label>
		<input type="text" class="textinput" name="subject" value="<?php echo $item->subject;?>" />
		
		<label>Creator</label>
		<input type="text" class="textinput" name="creator" value="<?php echo $item->creator;?>" />
		
		<label>Additional Creator</label>
		<input type="text" class="textinput" name="additional_creator" value="<?php echo $item->additional_creator;?>" />
		
		<label>Date</label>
		<input type="text" class="textinput" name="date" value="<?php echo $item->date;?>" />
	</fieldset>
	<fieldset id="type-metadata">
		<legend>Type Metadata</legend>
		<label>Collection</label>
			<?php select(	array(	
						'name'	=> 'collection_id',
						'id'	=> 'collection_id' ),
						$collections,
						$item->collection_id,
						'Collection',
						'id',
						'name' ); ?>
		
		<h3>Type Info</h3>
			<?php select(	array(	
						'name'	=> 'type_id',
						'id'	=> 'type_id' ),
						$types,
						$item->type_id,
						'Type Info',
						'id',
						'name' ); ?>
		
		
		<input type="submit" name="change_type" value="Pick this type" />
		<?php
			foreach($item->Type->Metafields as $metafield):
		?>

		<label for="Name[<?php echo $key;?>][text]"> Metafield name:</label> 
		<input type="text" class="textinput" value="Name[<?php echo $key;?>][text]" />
		<label for="Description[<?php echo $key;?>][text]"> Metafield description:</label> 
		<textarea name="Description[<?php echo $key;?>][text]"><?php echo $metafield->description; ?></textarea>
		<label for="Metatext[<?php echo $key;?>][text]"> Metatext: <textarea class="textinput" name="Metatext[<?php echo $key;?>][text]"><?php echo $item->Metatext[$key]->text; ?>"/></textarea>
		<input type="hidden" name="Metatext[<?php echo $key; ?>][metafield_id]" value="<?php echo $metafield->id; ?>"/>

		<?php	
			endforeach;
		?>	
		
		<!-- MAX_FILE_SIZE must precede the file input field -->
		<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
		<input name="file[0]" type="file" class="textinput" />
		
	<label for="public">Item is public: <?php

			checkbox(array('name'=>'public', 'id'=>'public'), $item->public);

		?></label>
		<?php if(1==0):?>		
				<h3>Plugin Info</h3>
				<?php foreach($plugins as $plugin): ?>
					<label><?php echo $plugin->name?> Plugin</label>
					<?php foreach($plugin->Metafields as $metafield):?>
							<h5><?php echo $metafield->name; ?></h5>
							<h5><?php echo $metafield->description ?></h5>
					<?php endforeach;?>
				<?php endforeach;?>
		<?php endif; ?>
		</fieldset>

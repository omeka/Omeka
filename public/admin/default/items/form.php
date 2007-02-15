<?php error($item); ?>
		<h4>Title</h4>
		<input type="text" name="title" value="<?=$item->title;?>" size="50" />
		
		<h4>Publisher</h4>
		<input type="text" name="publisher" value="<?=$item->publisher?>" size="50" />
		
		<h4>Relation</h4>
		<input type="text" name="relation" value="<?=$item->relation;?>" size="50" />
		
		<h4>Language</h4>
		<input type="text" name="language" value="<?=$item->language;?>" size="50" />

		<h4>Coverage</h4>
		<input type="text" name="coverage" value="<?=$item->coverage;?>" size="50" />
		
		<h4>Rights</h4>
		<input type="text" name="rights" value="<?=$item->rights;?>" size="50" />
		
		<h4>Description</h4>
		<input type="text" name="description" value="<?=$item->description;?>" size="50" />
		
		<h4>Source</h4>
		<input type="text" name="source" value="<?=$item->source;?>" size="50" />
		
		<h4>Subject</h4>
		<input type="text" name="subject" value="<?=$item->subject;?>" size="50" />
		
		<h4>Creator</h4>
		<input type="text" name="creator" value="<?=$item->creator;?>" size="50" />
		
		<h4>Additional Creator</h4>
		<input type="text" name="additional_creator" value="<?=$item->additional_creator;?>" size="50" />
		
		<h4>Date</h4>
		<input type="text" name="date" value="<?=$item->date;?>" size="50" />
		
		<h4>Collection</h4>
		<p>Put a select here to pick the Collection.  Right now the Item belongs to a collection named: <?php echo $item->Collection->name; ?></p>
		
		<h3>Type Info</h3>
		<p>Put a select here to pick the Type.  Right now the Item has a Type called: <?php echo $item->Type->name; ?></p>
			
		<input type="submit" name="change_type" value="Pick this type" />
		<h4>Type Metadata</h4>
		<?php
			foreach($item->Type->Metafields as $key => $metafield):
		?>
		<h5> Metafield name: <?= $metafield->name; ?></h5>
		<h5> Metafield description: <?= $metafield->description; ?></h5>
		<h5> Metatext: <input type="text" name="Metatext[<?=$key;?>][text]" value="<?=$item->Metatext[$key]->text;?>"/></h5>
		<input type="hidden" name="Metatext[<?=$key;?>][metafield_id]" value="<?=$metafield->id;?>"/>
		<?php	
			endforeach;
		?>	

<?php if(1==0):?>		
		<h3>Plugin Info</h3>
		<?php foreach($plugins as $plugin): ?>
			<h4><?=$plugin->name?> Plugin</h4>
			<?php foreach($plugin->Metafields as $metafield):?>
					<h5><?=$metafield->name?></h5>
					<h5><?=$metafield->description?></h5>
			<?php endforeach;?>
		<?php endforeach;?>
<?php endif; ?>
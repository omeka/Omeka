<?php

	error($type);

?>
<script type="text/javascript" charset="utf-8">
	

	function ajaxify() {
		div = $('add');
		new Insertion.After(div, "<a href=\"#\" id=\"add_existing_metafield\">Add a pre-existing metafield</a>");
		new Insertion.After(div, "<a href=\"#\" id=\"add_new_metafield\">Add a new metafield</a>");
		
		Event.observe("add_existing_metafield", "click", addExistingMetafield);
		Event.observe("add_new_metafield", "click", addNewMetafield);
	}
	
	function getLastId() {
		input = $$("#metafields input").last();
		select = $$("#metafields select").last();		
		selectId = parseInt(select.id.replace("metafield_", ""));
		inputId = parseInt(input.id.replace("metafield_", ""));
		if(selectId > inputId) {
			return selectId;
		} else {
			return inputId;
		}
	}
	
	function addExistingMetafield() {
		var metafields = $A(null);
		<?php foreach( $metafields as $key => $metafield ):
			$metafield->load();
			echo "metafields[$key] = ".Zend_Json::encode($metafield->toArray()).";\n"; 
		endforeach; ?>		
		
		count = getLastId()+1;
		
		select = document.createElement("select");
		select.setAttribute('id', "metafield_"+count);
		select.setAttribute('name', "TypesMetafields["+count+"][metafield_id]");
		
		for(i=0;i<metafields.size();i++) {
			select.options[i] = new Option(metafields[i].name, metafields[i].id);
		}
		
		$('metafields').appendChild(select);
	}
	
	function addNewMetafield() {
		id = getLastId()+1;
		nameField = document.createElement("input");
		nameField.setAttribute("id", "metafield_"+id);
		nameField.setAttribute("name", "Metafields["+id+"][name]");
		descField = document.createElement("textarea");
		descField.setAttribute("name", "Metafields["+id+"][description]");
		$('metafields').appendChild(nameField);
		$('metafields').appendChild(descField);
	}
	
	Event.observe(window, "load", ajaxify);
</script>

<label for="name">Type Name</label>
<input class="textinput" type="text" name="name" value="<?php echo $type->name; ?>" />

<label for="description">Type Description</label>
<textarea class="textinput" name="description"><?php echo $type->description; ?></textarea>

<?php if($type->exists()): ?>
Edit existing metafields
<?php foreach( $type->Metafields as $key => $metafield ):
/*	select(	array(	
							'name'	=> "Metafields[$key][id]" ),
							$metafields,
							$metafield->id,
							'id',
							'name' ); */
	text(array('name' => "Metafields[$key][name]"),$metafield->name);
	echo 'Remove this metafield from the Type';
	checkbox(array('name' => "remove_metafield[$key]"));
	echo 'Delete this metafield permanently';
	checkbox(array('name' => "delete_metafield[$key]"));
endforeach; ?>

<?php endif; ?>

<div id="add">
</div>

<div id="metafields">
	Add a metafield:
	<?php if ( $metafields ): ?>
		<?php $totalMetafields = count($type->Metafields);?>

		<?php select(	array(	
							'name'	=> "TypesMetafields[$totalMetafields][metafield_id]",
							'id'	=> "metafield_$totalMetafields" ),
							$metafields,
							null,
							'Add a metafield',
							'id',
							'name' ); ?>
	<?php endif; ?>
	
	<input type="text" name="Metafields[<?php echo $totalMetafields;?>][name]" id="metafield_<?php echo $totalMetafields;?>" />
</div>

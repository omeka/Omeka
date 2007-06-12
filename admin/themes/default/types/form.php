<script type="text/javascript" charset="utf-8">
	

	function ajaxify() {
		div = $('add');
		new Insertion.After(div, "<a href=\"javascript:return false;\" id=\"add_existing_metafield\">Add a pre-existing metafield</a>");
		new Insertion.After(div, "<a href=\"javascript:return false;\" id=\"add_new_metafield\">Add a new metafield</a>");
		
		//hide the non-javascript metafield form elements
		$('new-metafields').descendants().each( function(element) {
			element.setStyle({display:"none"});
		});
		
		Event.observe("add_existing_metafield", "click", addExistingMetafield);
		Event.observe("add_new_metafield", "click", addNewMetafield);
	}
	
	function getLastId() {
		input = $$("#new-metafields input").last();
		select = $$("#new-metafields select").last();		
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
		
		$('new-metafields').appendChild(select);
	}
	
	function addNewMetafield() {
		id = getLastId()+1;
		
		nameFieldBox = document.createElement("div");
		nameFieldBox.setAttribute("class","field");
		nameFieldLabel = document.createElement("label");
		nameFieldLabel.innerHTML = 'Name Field';
		nameField = document.createElement("input");
		nameField.setAttribute("id", "metafield_"+id);
		nameField.setAttribute("name", "Metafields["+id+"][name]");
		nameField.setAttribute("class", "textinput");
		
		descFieldBox = document.createElement("div");
		descFieldBox.setAttribute("class","field");
		descFieldLabel = document.createElement("label");
		descFieldLabel.innerHTML = 'Description Field';
		descField = document.createElement("textarea");
		descField.setAttribute("name", "Metafields["+id+"][description]");
		descField.setAttribute("class", "textinput");
		
		nameFieldBox.appendChild(nameFieldLabel);
		nameFieldBox.appendChild(nameField);
		descFieldBox.appendChild(descFieldLabel);
		descFieldBox.appendChild(descField);
		
		$('new-metafields').appendChild(nameFieldBox);
		$('new-metafields').appendChild(descFieldBox);
		
	}
	
	Event.observe(window, "load", ajaxify);
</script>
<fieldset>
<div class="field">
<?php text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$type->name, 'Type Name'); ?>
</div>
<div class="field">
<?php textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description', 'rows'=>'10'),$type->description, 'Type Description'); ?>
</div>

<div id="old-metafields">
<?php if($type->exists()): ?>
<p>Edit existing metafields</p>
<?php foreach( $type->Metafields as $key => $metafield ):
	echo '<div class="field">';
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
	echo '</div>';
endforeach; ?>

<?php endif; ?>
</div>
<div id="add"></div>

<div id="new-metafields">
	<p>Add a metafield:</p>
	<?php if ( $metafields ): ?>
		<?php $totalMetafields = count($type->Metafields);?>

		<?php select(	array(	
							'name'	=> "TypesMetafields[$totalMetafields][metafield_id]",
							'id'	=> "metafield_$totalMetafields" ),
							$metafields,
							null,
							'Pre-existing Metafields',
							'id',
							'name' ); ?>
	<?php endif; ?>
	
	<input type="text" name="Metafields[<?php echo $totalMetafields;?>][name]" id="metafield_<?php echo $totalMetafields;?>" />
</div>
</fieldset>
<script type="text/javascript" charset="utf-8">
	

	function ajaxify() {
		var div = $('add');
		
		var addExisting = document.createElement('a');
		addExisting.href = "#";
		addExisting.id = "add-existing-metafield";
		addExisting.innerHTML = "Add a pre-existing metafield";
		$(addExisting).observe('click', function(e){
		    e.stop();
		    addMetafield('existing');
		});
		
		div.appendChild(addExisting);
		
		var addNew = document.createElement('a');
		addNew.href = "#";
		addNew.id = "add-new-metafield";
		addNew.innerHTML = "Add a new metafield";
		$(addNew).observe('click', function(e){
		    Event.stop(e);
		    addMetafield('new');
		});
		
		div.appendChild(addNew);
	}
	
	function getLastId() {
		input = $$("#new-metafields input").last();
		select = $$("#new-metafields select").last();
		if(!input && !select && div) {
			return 0;
		}
		if(select) {
			selectId = parseInt(select.id.replace("metafield-", ""));
		}else {
			selectId = 0;
		}		
		inputId = parseInt(input.id.replace("metafield-", ""));
		if(selectId > inputId) {
			return selectId;
		} else {
			return inputId;
		}
	}
	
	function addMetafield(type) {
		var uri = "<?php echo uri('types/add-metafield'); ?>"
		
		var index = getLastId() + 1;
		
		new Ajax.Request(uri, {
			parameters: {
    		    index: index,
    		    exists: (type == 'existing')
    		},
			onComplete: function(t) {
			    $('new-metafields').insert(t.responseText);
			}
		});
	}
	
	Event.observe(window, "load", ajaxify);
</script>
<?php echo flash(); ?>
<fieldset id="type-information">
	<legend>Type Information</legend>
<div class="field">
<?php text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$type->name, 'Type Name'); ?>
</div>
<div class="field">
<?php textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description', 'rows'=>'10'),$type->description, 'Type Description'); ?>
</div>
</fieldset>
<fieldset id="type-metafields">
	<legend>Type Metafields</legend>
	
<div id="existing-metafields">
<?php if($type->exists()): ?>
<h2>Edit existing metafields:</h2>
<?php foreach( $type->Metafields as $index => $metafield ):
	common('existing-metafield', compact('metafield', 'index'), 'types');
endforeach; ?>

<?php endif; ?>
</div>

<div id="new-metafields">
	<h2>Add a metafield:</h2>

<div id="add"></div>

	<?php $totalMetafields = count($type->Metafields);?>
	<?php common('new-metafield', array('id'=>$totalMetafields), 'types'); ?>
</div>
</fieldset>
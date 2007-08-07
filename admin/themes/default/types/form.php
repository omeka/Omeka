<script type="text/javascript" charset="utf-8">
	

	function ajaxify() {
		div = $('add');
		new Insertion.After(div, "<a href=\"javascript:void(0);\" id=\"add_existing_metafield\">Add a pre-existing metafield</a>");
		new Insertion.After(div, "<a href=\"javascript:void(0);\" id=\"add_new_metafield\">Add a new metafield</a>");
		
		$('add_existing_metafield').onclick = function() { addMetafield('old'); }
		
		$('add_new_metafield').onclick = function() { addMetafield('new'); }
	}
	
	function getLastId() {
		input = $$("#new-metafields input").last();
		select = $$("#new-metafields select").last();
		
		if(!input && !select) {
			return 0;
		}
		if(select) {
			selectId = parseInt(select.id.replace("metafield_", ""));
		}else {
			selectId = 0;
		}		
		inputId = parseInt(input.id.replace("metafield_", ""));
		if(selectId > inputId) {
			return selectId;
		} else {
			return inputId;
		}
	}
	
	function addMetafield(type) {
		switch(type) {
			case 'new':
				var uri = "<?php echo uri('types/_new_metafield'); ?>";
				break;
			case 'old':
				var uri = "<?php echo uri('types/_old_metafield'); ?>";
				break;
			default:
				break;
		}
		
		num = getLastId()+1;
		
		new Ajax.Request(uri, {
			parameters: "id=" + num,
			onComplete: function(t) {
				new Insertion.Bottom($('new-metafields'), t.responseText);
			}
		});
	}
	
	Event.observe(window, "load", ajaxify);
</script>
<fieldset>
	<legend>Edit Type</legend>
<div class="field">
<?php text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$type->name, 'Type Name'); ?>
</div>
<div class="field">
<?php textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description', 'rows'=>'10'),$type->description, 'Type Description'); ?>
</div>

<div id="old-metafields">
<?php if($type->exists()): ?>
<h2>Edit existing metafields:</h2>
<?php foreach( $type->Metafields as $key => $metafield ):
	echo '<div class="field">';
/*	select(	array(	
							'name'	=> "Metafields[$key][id]" ),
							$metafields,
							$metafield->id,
							'id',
							'name' ); */
	text(array('name' => "Metafields[$key][name]"),$metafield->name);
	echo '<span>Remove this metafield from the Type</span>';
	checkbox(array('name' => "remove_metafield[$key]"));
	echo '<span>Delete this metafield permanently</span>';
	checkbox(array('name' => "delete_metafield[$key]"));
	echo '</div>';
endforeach; ?>

<?php endif; ?>
</div>

<div id="new-metafields">
	<h2>Add a metafield:</h2>

<div id="add"></div>

	<?php $totalMetafields = count($type->Metafields);?>
	<?php common('_new_metafield', array('id'=>$totalMetafields), 'types'); ?>
</div>
</fieldset>
<?php 
	if(!isset($id)) {
		$id = $_REQUEST['id'];
	} 
?>


<div class="field">
	<label>Name Field</label>
	<input type="text" name="Metafields[<?php echo $id; ?>][name]" id="metafield_<?php echo $id; ?>" class="textinput" />
</div>


<div class="field">
	<label>Description Field</label>
	<textarea name="Metafields[<?php echo $id; ?>][description]" id="metafield_<?php echo $id; ?>" class="textinput"></textarea>
</div>
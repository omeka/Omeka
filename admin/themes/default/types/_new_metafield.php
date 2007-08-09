<?php 
	if(!isset($id)) {
		$id = $_REQUEST['id'];
	} 
?>

<div class="metafield" id="field_<?php echo $id; ?>">
<div class="field">
	<label for="Metafields[<?php echo $id; ?>][name]">Metafield Name</label>
	<input type="text" name="Metafields[<?php echo $id; ?>][name]" id="metafield_<?php echo $id; ?>" class="textinput" />
</div>

<div class="field">
	<label for="Metafields[<?php echo $id; ?>][description]">Metafield Description</label>
	<textarea name="Metafields[<?php echo $id; ?>][description]" id="metafield_<?php echo $id; ?>" class="textinput"></textarea>
</div>
</div>
<?php 
	if(!isset($id)) {
		$id = $_REQUEST['id'];
	} 
?>


<div class="field">
	<?php 
		select(array('name'=>'TypesMetafields['.$id.'][metafield_id]', 'id'=>'metafield_'.$id), metafields(), null, 'Choose an existing Metafield', 'id', 'name'); 
	?>
	
</div>
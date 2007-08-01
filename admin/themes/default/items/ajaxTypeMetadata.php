<?php 
	$_POST['change_type'] = true;
	
	if(!isset($id)) {
		$id = $_REQUEST['id'];
	}
	
	if($id) {
		$item = _make_omeka_request('Items','edit', array('id'=>$id), 'item');
	}else {
		$item = _make_omeka_request('Items', 'add', array(), 'item');
	}
	
	metatext_form($item,'textarea', $item->getTypeMetadata(false));
?>

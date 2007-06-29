<?php 
	$_POST['change_type'] = true;

	$item = _make_omeka_request('Items','edit', array('id'=>$_REQUEST['id']), 'item');
	
	metatext_form($item,'textarea');
?>

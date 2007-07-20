<?php 
	$_POST['change_type'] = true;

	if($_REQUEST['id']) {
		$item = _make_omeka_request('Items','edit', array('id'=>$_REQUEST['id']), 'item');
	}else {
		$item = _make_omeka_request('Items', 'add', array(), 'item');
	}
	metatext_form($item,'textarea');
?>

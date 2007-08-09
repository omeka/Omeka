<?php 
	$array = $user->toArray(); 
	
	$array['first_name'] = $user->first_name;
	$array['last_name'] = $user->last_name;
	$array['email'] = $user->email;
	
	$array['Flash'] = flash(false);
	
	echo Zend_Json::encode($array);
?>

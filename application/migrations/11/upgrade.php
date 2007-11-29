<?php 

	//The 'name' field for options is not long enough, let's try 200 characters
	$this->query("ALTER TABLE `options` CHANGE `name` `name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL"); 

	
?>

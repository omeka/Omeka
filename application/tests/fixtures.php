<?php 
	$tagTable = Doctrine_Manager::getInstance()->getTable('Tag');
	$taggingsTable = Doctrine_Manager::getInstance()->getTable('Taggings');
	$itemTable = Doctrine_Manager::getInstance()->getTable('Item');
	$conn = Doctrine_Manager::getInstance()->connection();
	
	$fixtures['Tag1'] = $tagTable->find(1);
	
	$fixtures['Tag2'] = $tagTable->find(2);
	
	$fixtures['Item Tags'] = $tagTable->findAll('Item');
	
	$fixtures['All Tags'] = $tagTable->findAll();
	
	$fixtures['First Item'] = $itemTable->find(1); 
	
	$fixtures['taggingCount']['Items'] = $taggingsTable->findBy(array(), 'Item', true);
	
	$fixtures['Item'] = $itemTable->find(1);
	
	$fixtures['Entity'] = Doctrine_Manager::getInstance()->getTable('Entity')->find(1);
	
	$fixtures['User'] = Doctrine_Manager::getInstance()->getTable('User')->find(1);
	
	$fixtures['User2'] = Doctrine_Manager::getInstance()->getTable('User')->find(2);
	
	$fixtures['Exhibit'] = Doctrine_Manager::getInstance()->getTable('Exhibit')->find(1);
?>

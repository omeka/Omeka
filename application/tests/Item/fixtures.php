<?php 
	$table = Doctrine_Manager::getInstance()->getTable('Item');
	
	$type = new Type;
	$type->name = "Bazfoo";
	$mf = new Metafield;
	$mf->name = "Bazfoo's Metafield";
	$type->Metafields[] = $mf;
	
	$type->save();
	
	$fixtures['Bazfoo Type'] = $type;
	
	$valid = $table->find(1);
	
	$valid->Type = $type;
	$valid->save();
	
	$fixtures['Valid Item'] = $valid;
	
	
	
	$fixtures['Invalid Item'] = $table->find(2);
?>

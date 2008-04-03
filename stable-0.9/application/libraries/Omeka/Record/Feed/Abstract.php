<?php 
/**
* 
*/
abstract class Omeka_Record_Feed_Abstract
{		
	abstract public function renderOne(Omeka_Record $record);
	
	abstract public function renderAll(array $records);	
}
 
?>

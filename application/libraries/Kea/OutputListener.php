<?php 
/**
 * Automatically wrap any accessed properties with allhtmlentities()
 *
 * @package Omeka
 **/
require_once 'Kea/View/UnicodeFunctions.php';
class Kea_OutputListener extends Doctrine_EventListener
{	 
	public function onGetProperty(Doctrine_Record $record, $property, $value)
    {
        return allhtmlentities($value);
    }
}
?>

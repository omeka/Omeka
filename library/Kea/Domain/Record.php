<?php

/**
 * Kea_Domain_Record 
 *
 * Customized wrapper for Doctrine_Record
 *
 * @package Kea
 * @author Kris Kelly
 **/
abstract class Kea_Domain_Record extends Doctrine_Record
{
	
	public function save(Doctrine_Connection $conn = null)
	{
		try{
			parent::save($conn);
			return true;
		} catch (Doctrine_Validator_Exception $e) {
			return $this->getErrorStack();
		}
	}
} // END abstract class Kea_Domain_Record

?>
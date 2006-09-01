<?php

class Metafield extends Kea_Domain_Model
{
	public $metafield_id;
	public $metafield_name;
	public $metafield_description;
	
	public static function uniqueName( $name )
	{
		$inst = new self;
		return $inst->mapper()->unique( 'metafield_name', $name );
	}
	
	public static function findBy( $col, $val )
	{
		$inst = new self;
		return $inst->mapper()->find( $col )->where( "$col = ?", $val )->execute();
	}
}

?>
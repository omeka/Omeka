<?php

class Category extends Kea_Domain_Model
{
	public $category_id;
	public $category_name;
	public $category_description;
	public $category_active;
	
	/**
	 * Metafields Collection Object
	 */
	public $metafields;
	
	public function getMetafields()
	{
		$this->metafields = self::getMapper('Metafield')->findByCategory( $this->category_id );
	}
	
	public function unqiue( $col, $val )
	{
		return $this->mapper()->unique( $col, $val );
	}
	
	public static function uniqueName( $name )
	{
		return self::getMapper(__CLASS__)->unique( 'category_name', $name );
	}
}

?>
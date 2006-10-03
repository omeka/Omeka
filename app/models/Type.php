<?php

class Type extends Kea_Domain_Model
{
	public $type_id;
	public $type_name;
	public $type_description;
	public $type_active;
	
	/**
	 * Metafields Collection Object
	 */
	public $metafields;
	
	public function getMetafields()
	{
		$this->metafields = self::getMapper('Metafield')->findByType( $this->type_id );
	}
	
	public function unqiue( $col, $val )
	{
		return $this->mapper()->unique( $col, $val );
	}
	
	public static function uniqueName( $name )
	{
		return self::getMapper(__CLASS__)->unique( 'type_name', $name );
	}
	
	public function removeMetafieldAssoc( Metafield $metafield )
	{
		if( !$metafield->getId() )
		{
			throw new Kea_Domain_Exception('Metafield needs a valid ID.');
		}
		$join_mapper = Kea_Domain_HelperFactory::getMapper('TypesMetafields');
		return $join_mapper->delete( $this->getId(), $metafield->getId() );
	}
	
	public function addMetafieldAssoc( Metafield $metafield )
	{
		if( !$metafield->getId() )
		{
			throw new Kea_Domain_Exception('Metafield needs a valid ID.');
		}
		$join_mapper = Kea_Domain_HelperFactory::getMapper('TypesMetafields');
		return $join_mapper->insert( $this->getId(), $metafield->getId() );
	}	
	
	public function hasMetafield( Metafield $metafield )
	{
		foreach( $this->metafields as $key => $mf )
		{
			if( ($mf instanceof Metafield && $mf->getId() == $metafield->getId() ) 
				|| (is_array($mf) && $mf['metafield_id'] == $metafield->getId() ) )
			{
				return TRUE;
			}
		}
		return FALSE;
	}
}

?>
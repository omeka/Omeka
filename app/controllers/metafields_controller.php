<?php

class MetafieldsController extends Kea_Action_Controller
{
	protected function _all( $type = 'object' )
	{
		$mapper = new Metafield_Mapper;
		switch( $type ) {
			case( 'object' ):
				return $mapper->allObjects();
			break;
			case( 'array' ):
				return $mapper->allArray();
			break;
		}
		return false;
	}
	
	protected function _findIDByName( $name )
	{

		$mapper = new Metafield_Mapper();
		$stmt = $mapper->select()->where( 'metafield_name = ?', $name );
		$result = $mapper->query( $stmt );
		if( $result->num_rows == 1 ) {
			$stuff =  $mapper->load( $result );
			print_r($stuff); exit;
		}			
	}
	
}

?>
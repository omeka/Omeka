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
}

?>
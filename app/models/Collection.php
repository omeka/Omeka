<?php

class Collection extends Kea_Domain_Model
{
	public $collection_id;
	public $collection_name;
	public $collection_description;
	public $collection_active;
	public $collection_collector;
	
	public function __construct( $array = null )
	{
		if( isset( $array['collection_active'] )
			&& ( $array['collection_active'] == 1
				|| $array['collection_active'] == 'on' ) )
		{
			$array['collection_active'] = '1';
		}
		else
		{
			$array['collection_active'] = '0';
		}
		
		parent::__construct( $array );
	}
}

?>
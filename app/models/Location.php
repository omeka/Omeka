<?php

class Location extends Kea_Domain_Model
{
	public $location_id;
	public $object_id;
	public $latitude;
	public $longitude;
	public $address;
	public $zipcode;
	public $zoomLevel;
	public $mapType;
	public $cleanAddress;
	
	protected $validate;
	
	public function hasValues()
	{
		if( ( !empty( $this->latitude ) && !empty( $this->longitude ) ) || ( !empty( $this->address ) || !empty( $this->zipcode ) ) )
		{
			return true;
		}
		return false;
	}
}

?>
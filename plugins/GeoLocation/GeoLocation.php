<?php
/**
 * 
 *
 * @package Omeka
 * 
 **/
class GeoLocation extends Kea_Plugin
{	
	protected $metaInfo = array(
			'description'=>'Uses the Google Maps API to allow Items to be associated with a geographical location.',
			'author'=>'Center for History & New Media');
	
	public function definition() {
		$this->hasConfig('Default Latitude', 'The default latitude for the map.', 50);
		$this->hasConfig('Default Longitude', 'The default longitude for the map.', 70);
		$this->hasConfig('Default ZoomLevel', 'The default zoom level for the map.', 5);
		$this->hasConfig('Google Maps API Key', 'The API key (plugin will not work properly without this).');
		
		$this->hasMetafield('Map Latitude', 'The latitude on the map.');
		$this->hasMetafield('Map Longitude', 'The longitude on the map.');
		$this->hasMetafield('Map Zoom Level', 'The zoom level on the map.');
		$this->hasMetafield('Map Street Address');
		$this->hasMetafield('Map Zipcode');
		
		$this->hasType('Building', 'A man-made edifice', 
			array(
				array('name'=>'City', 'description'=>'The city in which a building is located.'),
				array('name'=>'County', 'description'=>'The county in which a building is located.'),
				array('name'=>'Owner Name', 'description'=>'The name of the person or entity who owns the building.')));
				
		$this->typeHasMetafield('Event', 'Extra Map Field', 'This is a fake metafield to test adding metafields to types.');
	}
	
	public function addNavigation($text, $link) {
		if($text == 'Themes') {
			return array('Foo', $this->uri('bar'));
		}
	}
	
	public function routeStartup()
    {
//        $this->getResponse()->appendBody('<p>routeStartup() called</p>');
    }

    public function routeShutdown($request)
    {
//        $this->getResponse()->appendBody('<p>routeShutdown() called</p>');
    }

    public function dispatchLoopStartup($request)
    {
//        $this->getResponse()->appendBody('<p>dispatchLoopStartup() called</p>');
    }

    public function preDispatch($request)
    {
//        $this->getResponse()->appendBody('<p>preDispatch() called</p>');
    }

    public function postDispatch($request)
    {
//        $this->getResponse()->appendBody('<p>postDispatch() called</p>');
    }

    public function dispatchLoopShutdown()
    {
//        $this->getResponse()->appendBody('<p>dispatchLoopShutdown() called</p>');
    }

} // END class FooPlugin

	/**
	 * 
	 * 1) Note: the width and height setters will probably break in IE, I forgot how to fix this
	 * 3) If you want to submit these values via a form, be sure to include fields where id = latitude, longitude and zoomLevel, and each
	 * is prefixed with the $divName
	 * 4) This is pretty simple, but we can add all kinds of options to this in the future.
	 *
	 * @todo Add an option to specify an onClick handler that has been written in JavaScript elsewhere
	 * 
	 * @param int center latitude
	 * @param int center longitude
	 * @param int zoom level
	 * @param int width of map
	 * @param int height of map
	 * @param string ID of the map's (empty) div
	 * @param array Multidimensional Array like: $points[0]['latitude'] = 75, $points[0]['longitude'] = 40, etc.
	 * @param array extra options
	 * @return string
	 **/
	function google_map($latitude, $longitude, $zoomLevel, $width, $height, $divName = 'map', $uri, $options = array()) {
		echo "<div id=\"$divName\"></div>";
		//Load this junk in from the plugin config
		if(!$latitude || !$longitude) {
			$latitude = $this->getConfig('Default Latitude');
			$longitude = $this->getConfig('Default Longitude');
			$zoomLevel = $this->getConfig('Default Zoom Level');
		}
		
		require_once 'Zend/Json.php';
		$options = Zend_Json::encode($options);
		echo "<script>var $divName = new OmekaMap('$divName', '$uri', $latitude, $longitude, $zoomLevel, $width, $height, $options);</script>";
	}

?>

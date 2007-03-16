<?php
/**
 * 
 *
 * @package Omeka
 * 
 **/
class GeoLocation extends Kea_Plugin
{	
	public function filterBrowse($browse) {
		if($browse->getClass() == 'Item') {
//			$browse->addSql('where', "items.description = 'foo'");
		}
	}	
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
	public function map($latitude, $longitude, $zoomLevel, $width, $height, $divName = 'map', $uri, $options = array()) {
		echo "<div id=\"$divName\"></div>";
		//Load this junk in from the plugin config
		if(!$latitude || !$longitude) {
			$latitude = $this->getConfig('Latitude');
			$longitude = $this->getConfig('Longitude');
			$zoomLevel = $this->getConfig('Zoom Level');
		}
		
		require_once 'Zend/Json.php';
		$options = Zend_Json::encode($options);
		echo "<script>var $divName = new OmekaMap('$divName', '$uri', $latitude, $longitude, $zoomLevel, $width, $height, $options);</script>";
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
	
	public function onCreate(Doctrine_Record $record) {
//		$this->insertAfter('Created an instance of '.get_class($record), 'called</p>');
	}
} // END class FooPlugin

?>

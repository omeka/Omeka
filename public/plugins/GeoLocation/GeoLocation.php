<?php
/**
 * 
 *
 * @package Omeka
 * 
 **/
class GeoLocation extends Kea_Plugin
{
	protected $config = array(
		'Latitude' => 50, 
		'Longitude' => 70, 
		'ZoomLevel' => 5, 
		'Maps Key' => 'ABQIAAAAD-SKaHlA87rO8jrVjT7SHBQ22YnqeXddIs-jHkCCm8C4K5z8GBTo29raXitwn3YbLGstzhF1Yn4Ctg');
	
	protected $metafields = array( array('name' => 'Latitude', 'description' => 'The latitude on the map.'), array('name' => 'Longitude', 'description' => 'The longitude on the map.'));
		
	public function header()
	{
		$key = $this->getConfig('Maps Key');
		echo '<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key='.$key.'" type="text/javascript"></script>';
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
	public function map($latitude, $longitude, $zoomLevel, $width, $height, $divName = 'map', $points = array(), $options = array()) {
	
		//Load this junk in from the plugin config
		if(!$latitude || !$longitude) {
			$plugin = Zend::Registry('GeoLocation');
			$latitude = $plugin->getConfig('Latitude');
			$longitude = $plugin->getConfig('Longitude');
			$zoomLevel = $plugin->getConfig('ZoomLevel');
		}

		//process the options
		$clickable = (bool) !empty($options['clickable']);
		$clickInJS = ($clickable) ? 'true' : 'false';
	
		$pointsJS = '';
		//IF there are other points, we would put them in here
		foreach( $points as $key => $point )
		{
			$pointsJS .= <<<POINTS
				point{$key} = new GLatLng({$point['latitude']}, {$point['longitude']});
				point{$key}marker = new GMarker(point{$key}, {clickable: $clickInJS});
				{$divName}.addOverlay(point{$key}marker);
POINTS;
		}
	
		// If there are no points given, add an overlay to the center
		$centerOverlayJS = '';
		if(!count($points)) {
			$centerOverlayJS .= <<<CENTER
				{$divName}marker = new GMarker({$divName}center, {clickable: $clickInJS});
				$divName.addOverlay({$divName}marker);
CENTER;
		}
				
		$javascript = <<<JAVA1
	<script type="text/javascript" charset="utf-8">

	  var $divName = null;
	  function {$divName}load() {
	     if(document.getElementById("$divName")) {
			if (GBrowserIsCompatible()) {
		 		document.getElementById("$divName").style.width = $width;
				document.getElementById("$divName").style.height = $height;
			
		      	$divName = new GMap2(document.getElementById("$divName"));
				{$divName}center = new GLatLng($latitude, $longitude);
		       $divName.setCenter( {$divName}center, $zoomLevel);
			   $divName.addControl(new GSmallMapControl());
				$centerOverlayJS
				$pointsJS
			
				{$divName}geocoder = new GClientGeocoder();

				if(document.getElementById('{$divName}latitude'))	{
	
					GEvent.addListener($divName, "click", function(marker, point) {
					  if (marker) {
					    $divName.removeOverlay(marker);
						document.getElementById('{$divName}latitude').value = null;
						document.getElementById('{$divName}longitude').value = null;
					  } else {
						$divName.clearOverlays();
					    $divName.addOverlay(new GMarker(point));
						document.getElementById('{$divName}zoomLevel').value = $divName.getZoom();
						document.getElementById('{$divName}latitude').value = point.lat();
						document.getElementById('{$divName}longitude').value = point.lng();
					  }
					});
				}
		     }
		  }
	   }
	
		oldonload = window.onload;
	    if (typeof window.onload != 'function') {
	    	window.onload = {$divName}load;
	  	} else {
	    	window.onload = function() {
	      		oldonload();
	      		{$divName}load();
	    	}
	  	}
	</script>
	<div id="{$divName}"></div>		
JAVA1;

		echo  $javascript;
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

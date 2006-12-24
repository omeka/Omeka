<?php
$__c->admin()->protect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title><?php echo SITE_TITLE; ?> -Admin-</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en-us" />
	<meta name="Copyright" content="Copyright (c) CHNM - Center for History and New Media chnm.gmu.edu" />
	<link rel="stylesheet" href="<?php $_link->style( 'main.css' ); ?>" type="text/css" />
	<?php $_common->javascripts( 'prototype.js', 'scriptaculous.js', 'CalendarPopup.js' ); ?>
	
		<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=<?php echo GMAPS_KEY; ?>" type="text/javascript"></script>


	    <script type="text/javascript">
	    //<![CDATA[
	    var map = null;
	    var geocoder = null;

	    function load() {
	      if (GBrowserIsCompatible()) {
	        map = new GMap2(document.getElementById("map"));
	        map.setCenter(new GLatLng(37.4419, -122.1419), 13);
			map.addControl(new GSmallMapControl());
	        geocoder = new GClientGeocoder();
			
			// Set the maptype
			document.getElementById('mapType').value = 'Google Maps API Version 2';
			
			GEvent.addListener(map, "click", function(marker, point) {
			  if (marker) {
			    map.removeOverlay(marker);
				document.getElementById('latitude').value = null;
				document.getElementById('longitude').value = null;
			  } else {
				map.clearOverlays();
			    map.addOverlay(new GMarker(point));
				document.getElementById('zoomLevel').value = map.getZoom();
				document.getElementById('latitude').value = point.lat();
				document.getElementById('longitude').value = point.lng();
			  }
			});
			
			/*
			GEvent.addListener( map, 'moveend', function()
			{
				document.getElementById('lat').value = 
				document.getElementById('lng').value = map.
				document.getElementById('zoom').value = map.zoomLevel();
			});
			*/

	      }
	    }

	    function showAddress() {
		var address = document.getElementById('address').value;
		var zip = document.getElementById('zipcode').value;
		
		if( address != '' && zip != '' )
		{
			address = address + ', ' + zip;	
		}
		else if( address == '' && zip != '' ) 
		{
			address = zip;
		}
		else if( address == '' && zip == '' )
		{
			alert( 'Please enter a valid address or zipcode.' );
		}

	      if (geocoder) {
			geocoder.getLocations(address, addAddressToMap);
	      }
	    }

	    function addAddressToMap(response) {
	      map.clearOverlays();
	      if (!response || response.Status.code != 200) {
	        alert("Sorry, we were unable to geocode that address.");
	      } else {
	        place = response.Placemark[0];
	        point = new GLatLng(place.Point.coordinates[1],
	                            place.Point.coordinates[0]);
			document.getElementById('cleanAddress').value = place.address;
			document.getElementById('latitude').value = point.lat();
			document.getElementById('longitude').value = point.lng();
	        marker = new GMarker(point);
	        map.addOverlay(marker);
	        marker.openInfoWindowHtml( '<strong>Is this the address you intended?</strong><br/>' + place.address );
	      }
	    }

	    //]]>
	    </script>
</head>
<body id="" onload="load()">
<div id="page">
	<div id="header">
		<?php include( $_partial->file( 'header' ) ); ?>
	</div>
	<div id="content">
		<?php include( $content_for_layout ); ?>
	</div>
	<div id="footer">
		<p>Developed by <a href="http://chnm.gmu.edu">CHNM</a></p>
	</div>
</div>
</body>
</html>


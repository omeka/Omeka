<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	
<title><?php echo SITE_TITLE; ?></title>

<!-- Meta elements -->
<?php include( $_partial->file( 'metalinks' ) ); ?>

<!-- Stylesheets -->
<?php include( $_partial->file( 'stylesheets' ) ); ?>

<!-- JavaScripts -->
<?php include( $_partial->file( 'javascripts' ) ); ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2.55&amp;key=<?php echo GMAPS_KEY; ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $_link->in('map.js','j'); ?>"></script>

<script type="text/javascript" src="<?php echo $_link->in('quickzoom.js','j'); ?>"></script>

<script type="text/javascript">
//<![CDATA[

var map;
var geocoder;
var icon;

// Creates the map.
function mapLoad()
{
	if (!document.getElementById("map")) return false;
	if (GBrowserIsCompatible())
	{
		map = new GMap2(document.getElementById("map"));
		map.setCenter(new GLatLng(28.03319784767635, -89.6044921875), 5);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
      	geocoder = new GClientGeocoder();
		document.getElementById('mapType').value = 'Google Maps API Version 2';
		<?php
			if( !empty( $saved['Location']['latitude'] ) && !empty( $saved['Location']['longitude'] ) )
			{
				echo 'savedPoint = new GLatLng( "'.$saved['Location']['latitude'].'", "'.$saved['Location']['longitude'].'");';
				echo 'map.addOverlay( new GMarker( savedPoint ) );';
				echo 'map.setZoom( 13 );';
				echo 'map.setCenter( savedPoint );';
			}
		?>
	
		// Create our "tiny" marker icon
		icon = new GIcon();
		icon.image = "http://labs.google.com/ridefinder/images/mm_20_red.png";
		icon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
		icon.iconSize = new GSize(12, 20);
		icon.shadowSize = new GSize(22, 20);
		icon.iconAnchor = new GPoint(6, 20);
		icon.infoWindowAnchor = new GPoint(5, 1);
		
		GEvent.addListener(map, "click", function(marker, point) {
		  if (marker) {
		    map.removeOverlay(marker);
			document.getElementById('latitude').value = null;
			document.getElementById('longitude').value = null;
		  } else {
			map.clearOverlays();
		    map.addOverlay(new GMarker(point) );
			document.getElementById('zoomLevel').value = map.getZoom();
			document.getElementById('latitude').value = point.lat();
			document.getElementById('longitude').value = point.lng();
		  }
		});
	}
}
	
function revealSwitch( field, file )
{
	new Ajax.Updater(	field,
		'<?php echo $_link->to(); ?>' + file,
		{
			onComplete: function(t) {
				new Effect.BlindDown( field, {duration: 0.8} );
			}
		});
}

function revealContributeChoice() {
	if(!document.getElementById) return false;
	addFile = document.getElementById('addfile');
	addStory = document.getElementById('addstory');

	addFile.onclick = function() {
		revealSwitch( 'contribute-choice', 'ajaxContributeFile');
		return false;
	}
	addStory.onclick = function() {
		revealSwitch( 'contribute-choice', 'ajaxContributeStory');
		return false;
	}
}

// Load Listeners
addLoadListener(mapLoad);
addLoadListener(revealContributeChoice);

//]]>
</script>

</head>
<body id="contribute" onunload="GUnload()">
<div id="wrap">
	<?php include( $_partial->file( 'header' ) ); ?>
	<div id="content">
		<?php include( $content_for_layout ); ?>
	</div>
	<?php include( $_partial->file( 'footer' ) ); ?>
</div>
</body>
</html>
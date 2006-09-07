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
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo GMAPS_KEY;?>"></script>
<script type="text/javascript" src="<?php echo $_link->in('map.js','j'); ?>"></script>

</head>
<body id="browse" onload="loadMap()" class="map" onunload="GUnload()">

<div id="wrap">
	<?php include( $_partial->file( 'header' ) ); ?>
	<div id="content">
		<?php include( $content_for_layout ); ?>
	</div>
	<?php include( $_partial->file( 'footer' ) ); ?>
</div>
<object id="obj" class="hide"></object>
<script type="text/javascript">
//<![CDATA[

//Declare the map outside of the getMap function; fixes a reported bug
var map;
var mapClick = 0;
var markerArray = new Array;
var featuredObject;

function openFeatured()
{
	featuredObject.openInfoWindowHtml(featuredObject.html);
}

function openInfo(i){
	markerArray[i].openInfoWindowHtml(markerArray[i].html);
}

function quickZoom( val ){
	if(val != 'null'){
	var coordinates = val.split(',');
	map.setCenter(new GLatLng(coordinates[0], coordinates[1]), 3);
	}
}

function createMarker(point, html) {
	var marker = new GMarker(point);
	marker.html = html;

	markerArray.push( marker );

	GEvent.addListener(marker, "click", function() {
		marker.openInfoWindowHtml(html);
	});

	return marker;
}

function buildPagination( page, per_page, total )
{
	pages = Math.ceil( total / per_page );
	if( page > 1 )
	{
		back = document.createElement('a');
		back.setAttribute('href', 'javascript:void(0)');
		back.onclick = function() {
			findMapItems('<?php echo $_link->to('mapXML'); ?>'+(parseInt(page)-1));
		}
		back.innerHTML = '<<';
	}
	else
	{
		back = document.createTextNode('<<');
	}
	
	current = document.createTextNode( ' '+page+' ' );
	
	if( page < pages )
	{
		forward = document.createElement('a');
		forward.setAttribute('href', 'javascript:void(0)');
		//forward.setAttribute('onclick', 'findMapItems("<?php echo $_link->to('mapXML'); ?>'+(parseInt(page)+1)+'")');
		forward.onclick = function() {
			findMapItems('<?php echo $_link->to('mapXML'); ?>'+(parseInt(page)+1));
		}
		forward.innerHTML = '>>';
	}
	else
	{
		forward = document.createTextNode('>>');
	}
	
	of = document.createTextNode(' of '+pages+' pages.');
	
	// Build the html
	span = document.getElementById('map-paginate');
	span.innerHTML = '';
	span.appendChild(back);
	span.appendChild(current);
	span.appendChild(forward);
	span.appendChild(of);
}

function findMapItems( link )
{	
	GDownloadUrl( link, function( data, responseCode ) {
		var xml = GXml.parse( data );
		var mapDetails = xml.documentElement;
		page = mapDetails.getAttribute('page');
		per_page = mapDetails.getAttribute('per_page');
		total = mapDetails.getAttribute('total');
		buildPagination( page, per_page, total );

		var markers = xml.documentElement.getElementsByTagName( "item" );
		if( markers.length > 0 )
		{
			map.clearOverlays();
			markerArray = new Array;
			regContent = document.getElementById('reg-content');
			regContent.innerHTML = '';
		}

		for( var i = 0; i < markers.length; i++ ) {
			id = markers[i].getAttribute( "id" );

			latitude = markers[i].getAttribute( "latitude" );
			longitude = markers[i].getAttribute( "longitude" );

		//	title = markers[i].getElementsByTagName( "title" )[0].firstChild.nodeValue;
			short_desc = markers[i].getElementsByTagName( "short_desc" )[0].firstChild.nodeValue;
			
			// Get Thumbnails for files
			files = markers[i].getElementsByTagName( "file" );
			if( files.length > 0 )
			{
				filename = files[0].getAttribute('file_thumbnail_name');
			}
			else
			{
				filename = '';
			}
			
			var point = new GLatLng( parseFloat( latitude ), parseFloat( longitude ) );

			// Build some html for under the map			
			regContent = document.getElementById('reg-content');

			html = document.createElement('div');

			html.className = 'reg-map-item';

			foo = '<p><a href="javascript:void(0);" onclick="openInfo('+i+')" class="maplink">Find this &#8594;</a> ' + short_desc + '</p>';
			
			html.innerHTML = foo;

			regContent.appendChild(html);


			// Build some html for the balloons
			balloon = '<div style="max-width:130px;" class="balloon">';
			if( filename != '' )
			{
				balloon += '<a href="<?php echo $_link->to("object"); ?>'+id+'"><img src="<?php echo WEB_THUMBNAIL_DIR.DS; ?>'+filename+'"  alt="'+short_desc+'" title="'+short_desc+'"/></a>';
			}
			else
			{
				balloon += '<div id="object" class="balloon-desc">'+ short_desc +'</div>';
				balloon += '<div class="balloon-footer"><a href="<?php echo $_link->to("object"); ?>'+id+'">View this object</a></div></div>';
			}
			map.addOverlay( createMarker( point, balloon ) );
		}
	});
}

function loadMap() {
	if (GBrowserIsCompatible()) {
		//All code from Google Map API
		//http://www.google.com/apis/maps/documentation/

		//Create the map
		map = new GMap2(document.getElementById("map"));

		//Map controls
		map.setCenter(new GLatLng(29.950974,-90.080798), 7);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		
		// We should be passing mapXML.php the current coordinates of the map, so that we can limit responses to the currently viewable area? [JMG]<?php echo $_link->to('+mapXML+'); ?>

		dataURL = "<?php $link = $_link->to('mapXML').'1'; if (@$_REQUEST['id']) $link .= '?id='. @$_REQUEST['id']; echo $link; ?>";
		findMapItems( dataURL );
	}
}

//]]>
</script>

</body>
</html>
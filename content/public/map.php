<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Collection | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo GMAPS_KEY;?>"
  type="text/javascript"></script>

<script type="text/javascript">
//<![CDATA[

//Declare the map outside of the getMap function; fixes a reported bug
var map;
var mapClick = 0;
var markerArray = new Array;
var featuredObject;

function loadMap() {

	if (GBrowserIsCompatible()) {
		//All code from Google Map API
		//http://www.google.com/apis/maps/documentation/

		//Create the map
		map = new GMap2(document.getElementById("map"));

		//Map controls
		map.setCenter(new GLatLng(28.03319784767635, -89.6044921875), 5);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		findMapItems( "<?php $link = $_link->to('mapXML'); if (@$_REQUEST['id']) $link .= '?id='. @$_REQUEST['id']; echo $link; ?>" );
		
	}

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

			title = markers[i].getElementsByTagName( "title" )[0].firstChild.nodeValue;
			short_desc = markers[i].getElementsByTagName( "short_desc" )[0].firstChild.nodeValue;
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

			foo = '<p onclick="openInfo('+i+')">' + title + '</p>';

			html.innerHTML = foo;

			regContent.appendChild(html);


			// Build some html for the balloons
			balloon = '<div class="balloon"><h4>'+title+'</h4>';
			if( filename != '' )
			{
				balloon += '<span class="balloon-img"><img src="<?php echo WEB_THUMBNAIL_DIR . DS; ?>'+filename+'" /></span>';
			}
			else
			{
				balloon += '<p class="balloon-desc">'+short_desc+'</p>';
			}
			balloon += '<p class="balloon-footer"><a href="<?php echo $_link->to('object'); ?>'+id+'">View this object</a></p></div>';
			map.addOverlay( createMarker( point, balloon ) );
		}
		
		// Handle featured object
		/*
		var featured = xml.documentElement.getElementsByTagName( "featured" );
		if( featured.length > 0 )
		{
			map.removeOverlay(featuredObject);
			featuredContent = document.getElementById('featured-content');
			featuredContent.innerHTML = '';
		}

		for( var i = 0; i < featured.length; i++ ) {
			id = featured[i].getAttribute( "id" );
			latitude = featured[i].getAttribute( "latitude" );
			longitude = featured[i].getAttribute( "longitude" );

			title = featured[i].getElementsByTagName( "title" )[0].firstChild.nodeValue;
			short_desc = featured[i].getElementsByTagName( "short_desc" )[0].firstChild.nodeValue;
			files = featured[i].getElementsByTagName( "file" );
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
			featuredContent = document.getElementById('featured-content');
			html = document.createElement('div');
			//html.setAttribute( 'class', 'fet-map-item' );
			html.className = 'fet-map-item';
		//	html.setAttribute( 'onclick', 'openFeatured()' );
			html.onclick = function() {
				openFeatured();
			}
			header = document.createElement('p');
			header.innerHTML = title;
			html.appendChild(header);
			
			featuredContent.appendChild(html);
			
			// Build some html for the balloons
			balloon = '<h4>Featured:'+title+'</h4>';
			if( filename != '' )
			{
				balloon += '<img src="<?php echo WEB_THUMBNAIL_DIR . DS; ?>'+filename+'" />';
			}
			balloon += '<p class="balloon-desc">'+short_desc+'</p>';
			balloon += '<p class="balloon-footer"><a href="<?php echo $_link->to('object'); ?>'+id+'">View this object</a></p>';
			
			marker = new GMarker( point );
			marker.html = html;
			GEvent.addListener(marker, "click", function() {
		    	marker.openInfoWindowHtml(balloon);
		  	});
			featuredObject = marker;
			map.addOverlay( marker );
		}

		// Select a random pin and move to it
		if( markerArray.length > 0 )
		{
			randomMarker = Math.floor(Math.random()*(markerArray.length + 1));
			map.panTo(markerArray[randomMarker].getPoint());
		}
		*/
	});
}

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
	//	back.setAttribute('onclick', 'findMapItems("<?php echo $_link->to('mapXML'); ?>'+(parseInt(page)-1)+'")');
		back.onclick = function() {
			findMapItems('<?php echo $_link->to('+mapXML+'); ?>'+(parseInt(page)-1));
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
			findMapItems('<?php echo $_link->to('+mapXML+'); ?>'+(parseInt(page)+1));
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

addLoadEvent(roundCorners);
addLoadEvent(loadMap);
</script>

<style type="text/css" media="screen">
/* <![CDATA[ */
div#map {width:508px; height:400px; border: 1px solid #ccc; display:block; float:right; overflow:auto;}
div#map {width: 718px;}
div#primary {padding-top: 20px;}
	#map-contents { width: 200px; height: 400px; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; overflow:auto; padding:0 5px;float:left; display:block;}
	#map-contents {width: 708px;border: 1px solid #ccc; border-top:none !important; height: 200px;}
	#reg-content { display:block;}
	#featured-content { display:block; padding-bottom: 10px; border-bottom: 1px solid #ccc;}
	#featured-content, #reg-content {margin:0; padding:0;}
	div.fet-map-item, div.reg-map-item {margin-bottom:10px; padding:0; background: #fff; display:block;}
	div.fet-map-item:hover, div.reg-map-item:hover {cursor:pointer;}
	div.fet-map-item { background: #fff09e; margin-top: 10px; padding: 10px;}
	div.fet-map-item h3 {margin-top:0; color: #c60;}
	div.fet-map-item p, div.reg-map-item p {color: #369; text-decoration:underline;}
	.foobar {background: orange;}
	
	
	.balloon {width:200px; height:140px; display:block;overflow:auto;}
	.balloon h4 {font-size: 1.2em; display:none;}
	/* ]]> */
</style>
</head>

<body id="browse" class="map" onunload="GUnload()">
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>Browse</h2>
		<?php include("inc/secondarynav.php"); ?>
		<div id="primary">
			<div id="map"></div>
			<div id="map-contents">
				<div id="map-controls" style="display:block; float:right; width: 300px;margin-top: 20px; text-align:right;">
					<span id="map-paginate"></span>
				</div>
				<!-- <h4>Featured Contribution</h4>
				<div id="featured-content"></div>-->
				<h4 style="display:block; float:left; width: 300px;">Recent Contributions</h4> 
				<div id="reg-content" style="clear:both;"></div>
			</div>
		</div>
	</div>
<?php include("inc/footer.php"); ?>
</div>
<!-- Another stupid hack from our Redmond friends.  DO NOT DELETE THE DIV BETWEEN THESE COMMENTS -->
<div id="object"></div>
<!-- END:: Another stupid hack from our Redmond friends -->
</body>
</html>
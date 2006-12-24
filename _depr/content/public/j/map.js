
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
    alert("Sorry, we were unable to discover that address, if you're sure it's correct just leave, simply click on the map to place a pin closest to the address you described.");
  } else {
    place = response.Placemark[0];
    point = new GLatLng(place.Point.coordinates[1],
                        place.Point.coordinates[0]);
	map.setZoom( 13 );
	document.getElementById('cleanAddress').value = place.address;
	document.getElementById('zoomLevel').value = map.getZoom();
	document.getElementById('latitude').value = point.lat();
	document.getElementById('longitude').value = point.lng();
    marker = new GMarker(point);
    map.addOverlay(marker);
    marker.openInfoWindowHtml( '<strong>Is this the address (or close to it) you intended?</strong><br/>' + place.address );
  }
}


function findOnMap()
{
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
        geocoder.getLatLng(
          address,
          function(point) {
            map.setCenter(point, 13);
            var marker = new GMarker(point, icon);
            map.addOverlay(marker);
          }
        );
      }
}

function moveMap(x,y){
	map.setCenter(new GLatLng(x,y), 13);
}

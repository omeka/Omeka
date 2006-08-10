<?php

	$__c->public()->getConsent();
	
	if( self::$_request->getProperty( 'contribute_submit' ) )
	{
		$saved = self::$_request->getProperties();	
	}
	elseif( self::$_session->getValue( 'contribute_form_need_login' ) )
	{
		$saved = self::$_session->getValue( 'contribute_form_need_login' );
		self::$_request->setProperties( $saved );
		self::$_session->unsetValue( 'contribute_form_need_login' );
	}
	else
	{
		$saved = false;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Add Your Voice | Katrina&#8217;s Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>
<?php $_common->javascripts( 'prototype.js', 'scriptaculous.js', 'CalendarPopup.js' ); ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2.55&amp;key=<?php echo GMAPS_KEY; ?>" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
	
	var map;
	var geocoder;
	var icon;
	function load()
	{
		if (GBrowserIsCompatible())
		{
			map = new GMap2(document.getElementById("map"));
			geocoder = new GClientGeocoder()
			map.setCenter(new GLatLng(28.03319784767635, -89.6044921875), 5);
			map.addControl(new GSmallMapControl());
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

	var cal = new CalendarPopup('calDiv');
	cal.showNavigationDropdowns();
	cal.setReturnFunction("setMultipleValues2");
	function setMultipleValues2(y,m,d)
	{
	     document.getElementById('date_year').value = y;
	     document.getElementById('date_month').value = LZ(m);
	     document.getElementById('date_day').value = LZ(d);
	}

	function reveal(elem)
	{
		if( document.getElementById(elem).style.display == 'none' )
		{
			Effect.BlindDown( elem, {duration: 1});	
		}
	}
	
	function hide(elem)
	{
		if( document.getElementById(elem).style.display != 'none' )
		{
			Effect.BlindUp(elem, {duration: 0.6});
		}
	}
	
	function switchXbox( state, id )
	{
		if( state )
		{
			document.getElementById(id).checked = false;
		}
	}

	function addFile()
	{
		var filelist = document.getElementById('files');
		var input = document.createElement('li');
		//input.style.display = "none";
		filelist.appendChild( input );
		
		input.className = 'foo';
		input.innerHTML = '<input name="objectfile[]" type="file" /><a href="javascript:void(0);" onclick="removeFile( parentNode )">Remove</a>';
		
		
		//Effect.Appear( input, {duration: 0.4} );
	}

	function removeFile( node )
	{
	//	Effect.Fade( node, {duration: 0.4} );
	/*  setTimeout( function() { */document.getElementById('files').removeChild( node );/* }, 600);*/
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
	
	addLoadEvent(roundCorners);
	addLoadEvent(popUps);
	addLoadEvent(load);
//]]>
</script>
</head>

<body id="contribute" onunload="GUnload()">

<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>Add Your Voice</h2>
		<div id="primary">
			<form id="contribute-form" enctype="multipart/form-data" action="<?php echo $_link->to('contribute'); ?>" method="post">
			<!-- Step one -->
				<div id="contribute-intro">
					<h3>Contribute to <cite>Katrina&#8217;s Jewish Voices</cite></h3>
					<p>We welcome contributions about the impact of Hurricane Katrina on the Jewish communities of New Orleans and the Gulf Coast and the responses of American Jewry to this vast humanitarian crisis. Contributions can be submitted in a variety of forms, including emails, photos, essays, web pages, and other digital files.</p>
					<p><a href="<?php echo $_link->to('faq'); ?>">Need help? Visit our Frequently Asked Questions.</a></p>
				</div>

			<!-- Step two -->
				<div id="step-two" class="clear">
					<fieldset id="section1">
						<legend><span class="number">1</span> Choose and Enter Your Contribution</legend>
						<div id="contribute-choose-inst">
							<p>Select your contribution type below. You may contribute a story, an image, or any other file.  If you have previously contributed <a href="<?php echo $_link->to('login'); ?>">please sign in</a> now</p>
							<ul>
								<li><a href="javascript:void(0)" onclick="revealSwitch( 'contribute-choice', 'ajaxContributeStory');" >Type in your contribution</a></li>
								<li><a href="javascript:void(0)" onclick="revealSwitch( 'contribute-choice', 'ajaxContributeFile');" >Upload a file</a></li>
							</ul>
							<?php
								$_form->displayError( 'Object', 'empty_object_type', $__c->public()->validationErrors() );
							?>
							<em>The necessary form will load to the right.</em>
						</div>

						<div id="contribute-choice">
							<?php
							if( isset( $saved['online_story_text'] ) )
							{
								include( 'ajaxContributeStory.php' );
							}
							elseif( isset( $saved['MAX_FILE_SIZE'] ) )
							{
								include( 'ajaxContributeFile.php' );
							}
							else
							{
								//echo '<p>Select your contribution type on the left.</p>';
							}
							?>
						</div>

					</fieldset>
					<fieldset id="section2">
						<legend><span class="number">2</span> Describe your contribution</legend>
						<div style="display:block; float:left; width: 720px;">
						<div id="other-info">
						<label for="object_description">Any other information about the text/image/file you are submitting:</label>
						<?php
							$_form->textarea( array('cols'	=> 40,
													'rows'	=> 10,
													'name'	=> 'Object[object_description]',
													'id'	=> 'object_description' ),
											$saved['Object']['object_description'] );
						?>

						</div>
						
						<div id="creatorinput">
						<div style="display:block; float:right; width: 272px;">
						<label for="object_creator">Did you create this?</label>
						<?php
							$_form->radio(	'object_creator',
											array( 'yes' => 'Yes', 'no' => 'No' ),
											'yes',
											$saved['object_creator'] );
						?>
						</div>
						<label for="object_creator_other">If not, who did?</label>
						<?php
							$_form->text( array('name'	=> 'Object[creator_other]',
												'class'	=> 'textinput',
												'value'	=> $saved['Object']['creator_other'] ) );
						?>
						</div>
						<div id="dateinput">
						<h4>Date</h4>

						<label>What date is most relevant to your contribution?  This may be the date on which the event or story occurred, or one which it references.</label>
						<a href="javascript:void(0)" name="calAnchor" id="calAnchor" onclick="cal.showCalendar('calAnchor'); return false;">Select a Date</a><br /><br />
						<?php
							$_form->text( array('size'	=> 2,
												'id'	=> 'date_month',
												'name'	=> 'date[month]',
												'class' => 'textinput',
												'value'	=> $saved['date']['month'] ) );

							$_form->text( array('size'	=> 2,
												'id'	=> 'date_day',
												'name'	=> 'date[day]',
												'class' => 'textinput',
												'value'	=> $saved['date']['day'] ) );

							$_form->text( array('size'	=> 4,
												'id'	=> 'date_year',
												'name'	=> 'date[year]',
												'class' => 'textinput',
												'value'	=> $saved['date']['year'] ) );
						?>
						<em>( mm / dd / yyyy )</em>
						<div id="calDiv" style="position:absolute;visibility:hidden;background-color:#fff;"></div>

						</div>
						<div id="locationinput">
						<div id="location_info">
							<h4>Location</h4>
							
							<p>Define the location by using the map on the right to pan and zoom to the location, then click on the map on or near the location.</p>
							<p>You may also include an address and/or zip code.</p>

							<label for="object_location">Address</label>
							<?php
								$_form->text( array('class'	=> 'textinput',
													'name'	=> 'Location[address]',
													'id'	=> 'address',
													'size'	=> 20,
													'value'	=> $saved['Location']['address'] ) );
							?>

							<label for="zipcode">Zipcode</label>
							<?php
								$_form->text( array('class'	=> 'textinput',
													'name'	=> 'Location[zipcode]',
													'id'	=> 'zipcode',
													'size'	=> 5,
													'value'	=> $saved['Location']['zipcode'] ) );
							?>

							<?php
								$_form->hidden( array(	'name'	=> 'Location[mapType]',
														'id'	=> 'mapType',
														'value'	=> $saved['Location']['mapType'] ) );

								$_form->hidden( array(	'name'	=> 'Location[zoomLevel]',
														'id'	=> 'zoomLevel',
														'value'	=> $saved['Location']['zoomLevel'] ) );


								$_form->hidden( array(	'name'	=> 'Location[cleanAddress]',
														'id'	=> 'cleanAddress',
														'value'	=> $saved['Location']['cleanAddress'] ) );

								$_form->hidden( array(	'name'	=> 'Location[latitude]',
														'id'	=> 'latitude',
														'value'	=> $saved['Location']['latitude'] ) );

								$_form->hidden( array(	'name'	=> 'Location[longitude]',
														'id'	=> 'longitude',
														'value'	=> $saved['Location']['longitude'] ) );											
							?>
							<br />
							<input type="button" id="findonmap" class="input-submit" onclick="showAddress()" value="Find Address On Map" />
							<p><a href="<?php echo $_link->to('maphelp'); ?>" class="popup">Need help with the map?</a></p>
						</div>

						<div id="map"></div>
						</div>
						<div id="tagsinput">
						<h4>Tags</h4>
						<label class="instructions">Separate tags by comma. (<a href="<?php echo $_link->to('whataretags'); ?>" class="popup">What are tags?</a>)</label>
						<?php
							$_form->text( array( 	'class'	=> 'textinput',
													'id'	=> 'object_tags',
													'name'	=> 'object_tags',
													'value'	=> $saved['object_tags'] ) );
						?>
						</div>
					</fieldset>

					<fieldset id="section3">
						<legend><span class="number">3</span> Tell Us About Yourself</legend>
						<label for="contributor_permissions">In addition to saving your contribution to the archive, may we post it on this site?</label>					
						<?php
							$_form->select(	array(	'name'	=> 'Object[object_contributor_posting]',
													'id'	=> 'object_contributor_posting' ),
											array(	'yes'			=> 'Yes, including my name',
													'anonymously'	=> 'Yes, but without my name',
													'no'			=> 'No, just save it to the archive' ),
											$saved['Object']['object_contributor_posting'] );
						?>

					</fieldset>
					<?php $_form->displayError( 'Object', 'object_contributor_posting', $__c->public()->validationErrors() ); ?>

					<?php if( !self::$_session->getUser() || !self::$_session->getUser()->isContributor() ): ?>

						<p>What is your name?</p>
						<label for="contributor_first">First Name <span class="required">(Required)</span></label>

						<?php
							$_form->text( array(	'class' => 'textinput',
							 						'name'	=> 'Contributor[contributor_first_name]',
													'id'	=> 'contributor_first',
													'value'	=> $saved['Contributor']['contributor_first_name'] ) );
						?>
						<?php
							$_form->displayError( 'Contributor', 'contributor_first_name', $__c->public()->validationErrors() );
						?>

						<label for="contributor_last">Last Name <span class="required">(Required)</span></label>
						<?php
							$_form->text( array(	'class' => 'textinput',
							 						'name'	=> 'Contributor[contributor_last_name]',
													'id'	=> 'contributor_last',
													'value'	=> $saved['Contributor']['contributor_last_name'] ) );
						?>
						<?php
							$_form->displayError( 'Contributor', 'contributor_last_name', $__c->public()->validationErrors() );
						?>
						<?php if( !self::$_session->getUser() ): ?>
						<label for="contributor_email">What is your email address? <span class="required">(Required)</span> (Your email address will not be shared.)</label>

						<?php
							$_form->text( array(	'class'	=> 'textinput',
													'name'	=> 'Contributor[contributor_email]',
													'id'	=> 'contributor_email',
													'value'	=> $saved['Contributor']['contributor_email'] ) );
						?>	
						<label for="contributor_email_check">Please re-enter your email address.  By entering your email address again we can establish an account for you, or find your pre-existing account so you may track your contributions. <span class="required">(Required)</span></label>
						<?php
								$_form->text(	array(	'class'	=>	'textinput',
								 						'name'	=>	'contributor_email_check',
														'id'	=>	'contributor_email_check',
														'value'	=>	$saved['contributor_email_check'] ) );
						?>
						<?php $_form->displayError( 'Contributor', 'contributor_email', $__c->public()->validationErrors() ); ?>
						<?php $_form->displayError( 'Contributor', 'email_check', $__c->public()->validationErrors() ); ?>
						
						<?php endif; ?>
						
						<label class="radiolabel" id="contact-permission">
						<input type="checkbox" name="contributor_contact_consent" value="yes" checked="checked" />May we contact you about your contribution and with news about this project?</label>

					</fieldset>
				</div>

				<div id="form-continue">
					<p>Please consider providing more information about yourself. Doing so will help future historians understand Hurricane Katrina's impact on the American Jewish community.   If you would not like to provide more information, you may submit your contribution now.</p>
					<input type="button" class="input-submit" value="Continue with Form" onclick="revealSwitch('moreInfo', 'ajaxMoreInfo')" />

				<?php endif; ?>
					<input type="submit" class="input-submit" name="contribute_submit" value="Enter your Contribution -&gt;" onclick="return confirm('Are you sure you would like to submit your contribution now?');" />

					</fieldset>

				</div>

				<div id="moreInfo">
				<?php
					if( isset( $saved['Contributor']['contributor_birth_year'] ) )
					{
						include( 'ajaxMoreInfo.php' );
					}
				?>
				</div>
			</form> <!-- closes form -->
		</div> <!-- closes primary div -->
	</div> <!-- closes content div -->
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>
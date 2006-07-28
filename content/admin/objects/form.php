<fieldset class="formElement">
	<label for="object_status">The object's status as set by an authorized user.</label>
	<p class="instructionText">(required)</p>	
	<?php
		$_form->select(	array(	'name'	=> 'Object[object_status]',
								'id'	=> 'object_status' ),
						array(	'notyet'	=> 'Not yet considered',
						 		'moreinfo'	=> 'Additional contributor information needed',
								'review'	=> 'Administrative review needed',
								'approved'	=> 'Approved',
								'rejected'	=> 'Rejected' ),
						$saved['Object']['object_status']
					);
		$_form->displayError( 'Object', 'object_status', $__c->objects()->validationErrors() );
	?>
	
	
</fieldset>

<fieldset class="formElement">
	<label for="object_title">Title or Name</label>
	<p class="instructionText">(required)</p>
	<?php
		$_form->text(	array(	'name'		=> 'Object[object_title]',
								'id'		=> 'object_title',
								'size'		=> 40,
								'value'		=> $saved['Object']['object_title'] ) );

		$_form->displayError( 'Object', 'object_title', $__c->objects()->validationErrors() );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="object_description">Description</label>
	<p class="instructionText"></p>
	<?php 
		$_form->textarea( 	array(	'rows'	=> '8',
									'cols'	=> '60',
									'id'	=> 'object_description',
									'name'	=> 'Object[object_description]' ),
									$saved['Object']['object_description'] );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="category">Katrina's Jewish Voices Object Type</label><br/>
	<?php
		$_form->select( array(	'name'		=> 'Object[category_id]',
								'id'		=> 'category_id',
								'onchange'	=> 'getData(this.value, "ajax_category_form")' ),
						$__c->categories()->all( 'array' ),
						$saved['Object']['category_id'],
						'category_id',
						'category_name' );
	?>
</fieldset>
<div id="ajax_category_form" style="display:none;"></div>

<fieldset class="formElement">
	<label for="object_language">Language</label><br/>
	<?php
		$_form->radio(	'Object[object_language]',
						array( 'eng' => 'English', 'fra' => 'French', 'other' => 'Other' ),
						'eng',
						$saved['Object']['object_language'] );

		$_form->text(	array(	'name'	=> 'object_language_other',
		 						'value'	=> $saved['object_language_other'] ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_id">Select the contributor from the list below:</label><br/>
	<p class="instructionText"></p>
	<?php
		$_form->select(	array(	'name'	=> 'Object[contributor_id]',
								'id'	=> 'contributor_id' ),
						$__c->contributors()->all( 'array' , 'alpha'),
						$saved['Object']['contributor_id'],
						'contributor_id',
						array( 'contributor_first_name', 'contributor_last_name') );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="object_contributor_consent">The contributor's consent for submitting this object.</label><br/>
	<p class="instructionText">(required)</p>
	<?php
		$_form->radio(	'Object[object_contributor_consent]',
						array( 'yes' => 'Yes', 'unsure' => 'Unsure', 'restrict' => 'Restrict', 'no' => 'No', 'unknown' => 'Unknown' ),
						'unknown',
						$saved['Object']['object_contributor_consent'] );
	?>

</fieldset>

<fieldset class="formElement">
	<label for="object_contributor_posting">The contributor's consent for posting this object online.</label><br/>
	<p class="instructionText">(required)</p>
	<?php
	$_form->radio(	'Object[object_contributor_posting]',
					array( 'yes' => 'Yes', 'no' => 'No', 'anonymously' => 'Anonymously', 'unknown' => 'Unknown' ),
					'unknown',
					$saved['Object']['object_contributor_posting'] );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="collection_id">Collection</label><br/>
	<p class="instructionText">If this object belongs to a collection, select it below.</p>
	<?php
		$_form->select(	array(	'name'	=> 'Object[collection_id]',
								'id'	=> 'collection_id' ),
						$__c->collections()->all( 'array' ),
						$saved['Object']['collection_id'],
						'collection_id',
						'collection_name' );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="object_creator">Creator</label>
	<p class="instructionText">Did the contributor create this object?</p>
	<?php
		$_form->radio(	'creator',
						array( 'yes' => 'Yes', 'no' => 'No' ),
						'yes',
						$saved['creator'] );
	?>
	<p class="instructionText">If no, enter the creator's name:</p>
	<?php
		$_form->text(	array(	'size'	=> 20,
								'name'	=> 'creator_other',
								'id'	=> 'creator_other',
								'value'	=> $saved['creator_other'] ) );
	?>
</fieldset>
	
<fieldset class="formElement">
	<label for="object_publisher">Publisher</label>
	<p>An entity responsible for making the resource available.</p>
	<?php 
		$_form->text( 	array(	'size'	=> 40,
								'name'	=> 'Object[object_publisher]',
								'id'	=> 'object_publisher' ),
								$saved['Object']['object_publisher'] );
	?>
</fieldset>

<fieldset>
	<label for="object_rights">Rights</label>
	<p>Rights binding the object or legal conditions pertaining to the object.</p>
	<?php
		$_form->textarea( 	array(	'rows'	=> '8',
									'cols'	=> '60',
									'id'	=> 'object_rights',
									'name'	=> 'Object[object_rights]' ),
									$saved['Object']['object_rights'] );
	?>
</fieldset>

<fieldset>
	<label for="object_relation">Relation</label>
	<p></p>
	<?php
		$_form->textarea( 	array(	'rows'	=> '8',
									'cols'	=> '60',
									'id'	=> 'object_relation',
									'name'	=> 'Object[object_relation]' ),
									$saved['Object']['object_relation'] );
	?>
</fieldset>

<fieldset>
	<label for="object_date">Object Creation Date</label>
	<br/>
	<?php
		$_form->text( array(	'name'		=> 'object_creation_month',
		 						'id'		=> 'object_creation_month',
								'value'		=> $saved['object_creation_month'],
								'size'		=> 2,
								'maxlength' => 2,
								'onclick'	=> 'cal1.showCalendar( "object_creation_month" ); return false;'), ' / ' );

		$_form->text( array(	'name'		=> 'object_creation_day',
		 						'id'		=> 'object_creation_day',
								'value'		=> $saved['object_creation_day'],
								'size'		=> 2,
								'maxlength' => 2,
								'onclick'	=> 'cal1.showCalendar( "object_creation_day" ); return false;'), ' / ' );

		$_form->text( array(	'name'		=> 'object_creation_year',
		 						'id'		=> 'object_creation_year',
								'value'		=> $saved['object_creation_year'],
								'size'		=> 4,
								'maxlength' => 4,
								'onclick'	=> 'cal1.showCalendar( "object_creation_year" ); return false;') );
	?>
	<p>Month / Day / Year</p>
	<div id="cal1Div" style="position:absolute;visibility:hidden;background-color:#fff;z-index:10002;" ></div>
	<?php
		$_form->displayError( 'Object', 'object_date', $__c->objects()->validationErrors() );
	?>
</fieldset>

<fieldset>
	<label for="object_coverage">Object Date Range</label>

	<p>Start</p>
	<?php
		$_form->text( array(	'name'		=> 'object_coverage_start_month',
		 						'id'		=> 'object_coverage_start_month',
								'value'		=> $saved['object_coverage_start_month'],
								'size'		=> 2,
								'maxlength' => 2,
								'onclick'	=> 'cal2.showCalendar( "object_coverage_start_month" ); return false;'), ' / ' );

		$_form->text( array(	'name'		=> 'object_coverage_start_day',
		 						'id'		=> 'object_coverage_start_day',
								'value'		=> $saved['object_coverage_start_day'],
								'size'		=> 2,
								'maxlength' => 2,
								'onclick'	=> 'cal2.showCalendar( "object_coverage_start_day" ); return false;'), ' / ' );

		$_form->text( array(	'name'		=> 'object_coverage_start_year',
		 						'id'		=> 'object_coverage_start_year',
								'value'		=> $saved['object_coverage_start_year'],
								'size'		=> 4,
								'maxlength' => 4,
								'onclick'	=> 'cal2.showCalendar( "object_coverage_start_year" ); return false;') );
	?>
	<p>Month / Day / Year</p>
	
	<p>End</p>
	<?php
		$_form->text( array(	'name'		=> 'object_coverage_end_month',
		 						'id'		=> 'object_coverage_end_month',
								'value'		=> $saved['object_coverage_end_month'],
								'size'		=> 2,
								'maxlength' => 2,
								'onclick'	=> 'cal3.showCalendar( "object_coverage_end_month" ); return false;'), ' / ' );

		$_form->text( array(	'name'		=> 'object_coverage_end_day',
		 						'id'		=> 'object_coverage_end_day',
								'value'		=> $saved['object_coverage_end_day'],
								'size'		=> 2,
								'maxlength' => 2,
								'onclick'	=> 'cal3.showCalendar( "object_coverage_end_day" ); return false;'), ' / ' );

		$_form->text( array(	'name'		=> 'object_coverage_end_year',
		 						'id'		=> 'object_coverage_end_year',
								'value'		=> $saved['object_coverage_end_year'],
								'size'		=> 4,
								'maxlength' => 4,
								'onclick'	=> 'cal3.showCalendar( "object_coverage_end_year" ); return false;') );
	?>
	<p>Month / Day / Year</p>
	
	<div id="cal2Div" style="position:absolute;visibility:hidden;background-color:#fff;z-index:10001;" ></div>
	<div id="cal3Div" style="position:absolute;visibility:hidden;background-color:#fff;z-index:10000;" ></div>
</fieldset>

<fieldset>
	<label for="object_location">Select the location from the map</label>
	<br/>
    <div id="map" style="width: 620px; height: 300px"></div>
	<br/>
	<?php
	$_form->text( 	array(	'size'	=> 20,
							'name'	=> 'Location[address]',
							'id'	=> 'address',
							'value'	=> $saved['Location']['address'] ), 'Address (optional)' );
	?>
	<br/>
	<?php
	$_form->text( 	array(	'size'	=> 5,
							'name'	=> 'Location[zipcode]',
							'id'	=> 'zipcode',
							'value'	=> $saved['Location']['zipcode'] ), 'Zipcode (optional)' );
	?>
	<br/>
	<input type="button" value="Find by Address" onclick="showAddress()">
	<br/>
	<?php
	$_form->text( 	array(	'size'	=> 20,
							'name'	=> 'Location[latitude]',
							'id'	=> 'latitude',
							'value'	=> $saved['Location']['latitude'] ), 'Latitude' );
	?>
	<br/>
	<?php
	$_form->text( 	array(	'size'	=> 20,
							'name'	=> 'Location[longitude]',
							'id'	=> 'longitude',
							'value'	=> $saved['Location']['longitude'] ), 'Longitude' );
							
	$_form->hidden( array(	'name'	=> 'Location[mapType]',
							'id'	=> 'mapType',
							'value'	=> $saved['Location']['mapType'] ) );

	$_form->hidden( array(	'name'	=> 'Location[zoomLevel]',
							'id'	=> 'zoomLevel',
							'value'	=> $saved['Location']['zoomLevel'] ) );

	$_form->hidden( array(	'name'	=> 'Location[cleanAddress]',
							'id'	=> 'cleanAddress',
							'value'	=> $saved['Location']['cleanAddress'] ) );
	?>
</fieldset>

<fieldset>
	<label for="tags">Tags</label>
	<p class="instructionText">Words or phrases, separated by commas. (eg. katrina, south port, levy)</p>
	<?php
		$_form->textarea( 	array(	'rows'	=> '2',
									'cols'	=> '60',
									'id'	=> 'tags',
									'name'	=> 'tags' ),
									$saved['tags'] );
	?>
</fieldset>

<fieldset class="formElement">
<h1>Files</h1>
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
<!-- Name of input element determines name in $_FILES array -->
<div id="files"></div>
<p><a href="javascript:void(0);" onclick="addFile()">Attach a file</a></p>
</fieldset>
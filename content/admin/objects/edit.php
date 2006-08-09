<?php
// Layout: mapedit;
$object = $__c->objects()->edit();
$saved = self::$_session->getValue( 'object_form_saved' );
?>

<script type="text/javascript">
var ajax;
function getData(id, form) {
	if( id == '' ) {
		Effect.BlindUp( form, { duration: 0.6 } );
		setTimeout( "document.getElementById('" + form + "').innerHTML = null", 600 );
		return false;
	}
	
	var opt = {
		parameters:'id=' + id,
		method:'get',
		onComplete: function(t) {
			new Effect.BlindDown( form, {duration: 0.8} );
		}
	}
	
	ajax = new Ajax.Updater(form,'<?php echo $_link->to( "objects" ); ?>'+form,opt);
}

function addFile() {
	var input = document.createElement("div");
	input.style.display = "none";
	document.getElementById('files').appendChild( input );
	input.innerHTML = 'Attach this file: <input name="objectfile[]" type="file" /><a href="javascript:void(0);" onclick="removeFile( parentNode )">Remove</a>';
	Effect.Appear( input, {duration: 0.4} );
}

function removeFile( node ) {
	Effect.Fade( node, {duration: 0.4} );
	setTimeout( function() { document.getElementById('files').removeChild( node ) }, 600);
}

function showResponse(div) {
	Effect.BlindDown(div);
}

function removeTag( tag_id, object_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'object_id=' + object_id + '&tag_id=' + tag_id,
	    onSuccess: function() {
			new Effect.Fade( 'tag-' + tag_id );
	    },
	    onFailure: function() {
	        alert('Could not delete tag.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'objects', 'ajaxRemoveTag' ); ?>', opt);
}

function deleteFile( file_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'file_id=' + file_id,
	    onSuccess: function() {
			new Effect.Fade( 'file-' + file_id );
	    },
	    onFailure: function() {
	        alert('Could not delete file.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'objects', 'ajaxDeleteFile' ); ?>', opt);
}

function deleteObjectFromCategory( object_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'object_id=' + object_id,
	    onSuccess: function() {
			new Effect.Fade( 'category_form', {duration: 0.6} );
			new Effect.Appear( 'category_add', {duration: 0.6} );
			setTimeout( "document.getElementById('category_form').innerHTML = null", 600 );
	    },
	    onFailure: function() {
	        alert('Could not delete file.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'objects', 'ajaxDeleteObjectFromCategory' ); ?>', opt);	
}
</script>

<style type="text/css" media="screen">
	.form-error { color: red; font-size: 1.2em;}
	.inputReadOnly { background-color: #eee; cursor: pointer;}
</style>


<?php include( 'subnav.php' ); ?>

<br/>

<h1>Edit Object</h1>
<?php  ?>
<form method="post" action="<?php echo $_link->to( 'objects', 'edit' ).$object->object_id; ?>" enctype="multipart/form-data">

<fieldset>
<p class="instructionText">Object's Database ID (read only)</p>
<input type="text" class="inputReadOnly" name="Object[object_id]" value="<?php echo $saved['Object']['object_id']; ?>" readonly></input>
</fieldset>

<fieldset>
<p class="instructionText">Date object added (read only)</p>
<input type="text" class="inputReadOnly" name="object_added" value="<?php echo $saved['object_added']; ?>" readonly></input>
</fieldset>

<fieldset>
<p class="instructionText">Date object modified (read only)</p>
<input type="text" class="inputReadOnly" name="object_modified" value="<?php echo $saved['object_modified']; ?>" readonly></input>
</fieldset>

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

<?php if( $object->category_id ): ?>
<div id="category_add" style="display:none">
<fieldset class="formElement">
	<label for="category">Katrina's Jewish Voices Object Type</label><br/>
	<?php
		$_form->select( array(	'name'		=> 'Object[category_id]',
								'id'		=> 'category_id',
								'onchange'	=> 'getData(this.value, "ajax_category_form")' ),
						$__c->categories()->all( 'array' ),
						null,
						'category_id',
						'category_name' );
	?>
</fieldset>
<div id="ajax_category_form" style="display:none"></div>
</div>

<div id="category_form">
	<h2>KJV ObjectType: <?php echo $object->category_name ?></h2>
	<p class="instructionText">If you would like to assign this object to a new category, first remove it from the current category.  After it is removed, you will be able to assign it a new category.</p>
	<input type="button" value="Remove category &gt; &gt;" onclick="if( confirm( 'Are you sure you want to remove this KJV ObjectType and the associated metadata from this object?  All of the object\'s associated metadata will be lost.') ) { deleteObjectFromCategory('<?php echo $object->getId(); ?>'); }">
	<fieldset class="formElement">
		<label>Category Description:</label>
		<p><?php echo $object->category_description; ?></p>

		<?php $i=0; foreach( $object->category_metadata as $metafield ): ?>
			<fieldset class="formElement">
			<label><?php echo $metafield['metafield_name']; ?></label>
			<p><?php echo $metafield['metafield_description']; ?></p>
			<input type="hidden" name="metadata[<?php echo $i; ?>][metafield_id]" value="<?php echo $metafield['metafield_id']; ?>" />
			<?php if( !empty( $metafield['metatext_id'] ) ): ?>
			<input type="hidden" name="metadata[<?php echo $i; ?>][metatext_id]" value="<?php echo $metafield['metatext_id']; ?>" />
			<?php endif; ?>
			<?php switch ($metafield['metafield_id']) {
				case ('2'):
				case ('3'):
				case ('4'):
				case ('5'):
				case ('6'):
				case ('19'):
				case ('26'):
				case ('31'):
					echo '<textarea rows="4" cols="60" name="metadata['.$i.'][metatext_text]">'.$metafield['metatext_text'].'</textarea>';
					break;
				case ('7'):
				case ('8'):
				case ('9'):
				case ('10'):
				case ('11'):
				case ('12'):
				case ('13'):
				case ('14'):
				case ('15'):
				case ('16'):
				case ('17'):
				case ('18'):
				case ('20'):
				case ('21'):
				case ('22'):
				case ('23'):
				case ('24'):
				case ('25'):
				case ('27'):
				case ('28'):
				case ('29'):
				case ('30'):
				case ('32'):
					echo '<input type="text" class="textinput" name="metadata['.$i.'][metatext_text]" value="'.$metafield['metatext_text'].'"/>';
					break;
		} ?>
			</fieldset>
			
		<?php $i++; endforeach; ?>
	</fieldset>
</div>

<?php else: ?>
<fieldset class="formElement">
	<label for="category">Katrina's Jewish Voices Object Type</label><br/>
	<?php
		$_form->select( array(	'name'		=> 'Object[category_id]',
								'id'		=> 'category_id',
								'onchange'	=> 'getData(this.value, "ajax_category_form")' ),
						$__c->categories()->all( 'array' ),
						null,
						'category_id',
						'category_name' );
	?>
</fieldset>
<div id="ajax_category_form" style="display:none"></div>
<?php endif; ?>

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
						$__c->contributors()->all( 'array' ),
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
	
	$_form->hidden( array(	'name'	=> 'Location[location_id]',
							'id'	=> 'location_id',
							'value'	=> $saved['Location']['location_id'] ) );
	?>
</fieldset>

<fieldset>
	<label for="tags">Tags</label>
	<p class="instructionText">These are the tags added by every user.  Removing a tag, removes all the instances any user has applied this tag.</p>
	<ul>
	<?php foreach($object->tags as $tag ): ?>
	<li id="tag-<?php echo $tag['tag_id']; ?>"><?php echo $tag['tag_name']; ?><input type="button" value="X" onclick="if ( confirm('Are you sure you want to disassociate this object from this tag?') ){ removeTag( '<?php echo $tag['tag_id']; ?>', '<?php echo $object->getId(); ?>') };"></li>
	</ul>
	<?php endforeach; ?>
	<p class="instructionText">Add new tags here. (separate each tag by a comma, ',')</p>
	<input type="text" name="tags"></input>
</fieldset>

<fieldset class="formElement">
	<p class="instructionText">These are the associated files with this object</p>
	<ul>
	<?php foreach( $object->files as $file ): ?>
		<li id="file-<?php echo $file->getId(); ?>"><a href="javascript:void(0)" onclick="window.open();"><?php echo $file->file_original_filename; ?></a><input type="button" value="X" onclick="if( confirm( 'Are you sure you want to permanently remove this file from the object as well as the archive?' ) ){ deleteFile( '<?php echo $file->getId(); ?>' )}"></li>
	<?php endforeach; ?>
	</ul>
<p class="instructionText">You may add additional files below:</p>
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
<!-- Name of input element determines name in $_FILES array -->
<div id="files"></div>
<p><a href="javascript:void(0);" onclick="addFile()">Attach a file</a></p>
</fieldset>

<input type="submit" value="Edit Object &gt;&gt;" name="object_edit" />

</form>
<br/>
<form method="post" action="<?php echo $_link->to( 'objects', 'delete'); ?>">
	<input type="hidden" value="<?php echo $object->getId(); ?>" name="object_id" />
	<input type="submit" value="Delete Object &gt;&gt;" name="object_delete" onclick="return confirm( 'Are you sure you want to delete this object, all of it\'s files, tags, and other data from the archive?' );"></input>
</form>
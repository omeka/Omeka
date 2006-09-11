<?php
// Layout: default;

$__c->objects()->ingest();
	
if(self::$_request->getProperty('batch_output')) echo self::$_request->getProperty('batch_output');
?>


<?php include( 'subnav.php' ); ?>



<h1>Add Objects in a Batch</h1>

Contents of dropbox:
<form method="get" action="<?php echo $_link->to('objects', 'batchadd'); ?>">
	<label class="checkboxlabel" for="preview_headers">Preview Image headers: 
		<input type="checkbox" name="preview_exif" id="preview_exif" />EXIF
		<input type="checkbox" name="preview_iptc" id="preview_iptc" />IPTC
	</label>
	<input type="submit" name="preview" id="preview" value="Refresh" />
</form>
<?php 
	if(isset($contributor)) 
	{
		$contributor = $__c->contributors()->edit();
	}
	else
	{
		$contributor = $__c->contributors()->add();	
	}
	
	$__c->objects()->displayDropbox();
	
	//$collections = array(array('collection_id'	=> null, 'collection_name'	=>	'New Collection'));
	$collections = $__c->collections()->all( 'array', 'alpha' );

?>
<form method="post" action="<?php echo $_link->to('objects', 'batchadd'); ?>">
	<fieldset>
	<legend>Collection settings</legend>
		<label class="checkboxlabel" for="collection_active">New collections are active by default:<input type="checkbox" name="collection[collection_active]" id="collection_active" checked="checked" /></label>
		<label class="radiolabel" for="read_exif">Create descriptions/metadata from image headers:
		<?php	$_form->radio(	'use_image_headers',
								array( 'exif' => 'EXIF', 'iptc' => 'IPTC', 'none' => 'Do not read' ),
								null,
								null );  ?>
		</label>

		<label for="object_status">Choose default status for objects in new collections:</label>
		<?php	$_form->select(	array(	'name'	=> 'Object[object_status]',
									'id'	=> 'object_status' ),
							array(	'notyet'	=> 'Not yet considered',
							 		'moreinfo'	=> 'Additional contributor information needed',
									'review'	=> 'Administrative review needed',
									'approved'	=> 'Approved',
									'rejected'	=> 'Rejected' ),
							'approved'
						);
			$_form->displayError( 'Object', 'object_status', $__c->objects()->validationErrors() ); ?>
	<label for="collection_id">Pick a pre-existing collection to use (no collection by default)</label>
	<?php $_form->select(	array(	'name'	=> 'collection_id',
							'id'	=> 'collection_id' ),
							$collections,
							null,
							'collection_id',
							'collection_name');
	?>
	</fieldset>
	<fieldset class="formElement">
	<legend>Contributor's Name and Consent</legend>
	<label for="contributor_last">Contributor's institution</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_institution,
									'id'	=> 'contributor_institution',
									'class' => 'textinput',
									'name'	=> 'contributor[contributor_institution]' ) );

	?>
	<p class="instructions">Note: First/Last name are optional if institutional name is given.</p>
	<label for="contributor_first">Contributor's first name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_first_name,
									'id'	=> 'contributor_first_name',
									'class' => 'textinput',
									'name'	=> 'contributor[contributor_first_name]' ) );

		$_form->displayError( 'Contributor', 'contributor_first_name', $__c->contributors()->validationErrors() );
	?>

	<label for="contributor_middle">Contributor's middle name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_middle_name,
									'id'	=> 'contributor_middle_name',
									'class' => 'textinput',
									'name'	=> 'contributor[contributor_middle_name]' ) );
	?>
	(optional)
	<label for="contributor_last">Contributor's last name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_last_name,
									'id'	=> 'contributor_last',
									'class' => 'textinput',
									'name'	=> 'contributor[contributor_last_name]' ) );

		$_form->displayError( 'Contributor', 'contributor_last_name', $__c->contributors()->validationErrors() );
	?>

	
</fieldset>


<fieldset class="formElement">
	<legend>Contributor's Contact Information</legend>
	<label for="contributor_contact_consent">Contributor's contact consent</label>
	
	<?php
		$_form->radio(	'contributor[contributor_contact_consent]',
						array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
						'unknown',
						@$contributor->contributor_contact_consent );

		$_form->displayError( 'Contributor', 'contributor_contact_consent', $__c->contributors()->validationErrors() );
	?>
	<label for="contributor_email">Contributor's email address</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> @$contributor->contributor_email,
										'id'	=> 'contributor_email',
										'class' => 'textinput',
										'name'	=> 'contributor[contributor_email]' ) );
		?>
	<label for="contributor_phone">Contributor's phone number</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> @$contributor->contributor_phone,
										'id'	=> 'contributor_phone',
										'class' => 'textinput',
										'name'	=> 'contributor[contributor_phone]' ) );
		?>

	<label for="contributor_fax">Contributor's fax number</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> @$contributor->contributor_fax,
										'id'	=> 'contributor_fax',
										'class' => 'textinput',
										'name'	=> 'contributor[contributor_fax]' ) );
		?>
	
	<?php //include( ABS_CONTENT_DIR.ADMIN_THEME_DIR.DS.'contributors'.DS.'form.php' ); ?>
	</fieldset>
		<?php
		$_form->hidden( array( 'name' => 'Object[object_contributor_posting]',
								'id' => 'object_contributor_posting',
								'value' => 'yes'));
		$_form->hidden( array( 'name' => 'Object[object_contributor_consent]',
								'id' => 'object_contributor_consent',
								'value' => 'yes'));
		$_form->hidden( array( 'name' => 'Object[object_language]',
								'id' => 'object_language',
								'value' => 'eng'));
		$_form->hidden( array( 'name' => 'Object[object_publisher]',
								'id' => 'object_publisher',
								'value' => 'NULL'));
		$_form->hidden( array( 'name' => 'Object[user_id]',
								'id' => 'user_id',
								'value' => self::$_session->getUser()->getId() ));
		$_form->hidden( array( 'name' => 'Object[object_rights]',
								'id' => 'object_rights',
								'value' => 'NULL'));
		$_form->hidden( array( 'name' => 'Object[object_featured]',
								'id' => 'object_featured',
								'value' => 0));
		$_form->hidden( array( 'name' => 'Object[object_coverage_start]',
								'id' => 'object_coverage_start',
								'value' => 'NULL'));
		$_form->hidden( array( 'name' => 'Object[object_coverage_end]',
								'id' => 'object_coverage_end',
								'value' => 'NULL'));
		$_form->hidden( array( 'name' => 'Object[object_added]',
								'id' => 'object_added',
								'value' => date("Y-m-d H:i:s")));
		?>
	<input type="submit" id="batch_add_do" name="batch_add_do" value="Batch it!" />
</form>

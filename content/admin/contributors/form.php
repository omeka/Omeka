<fieldset class="formElement">
	<legend>Contributor's Name and Consent</legend>
	<label for="contributor_last">Contributor's institution</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_institution,
									'id'	=> 'contributor_institution',
									'name'	=> 'contributor[contributor_institution]' ) );

	?>
	
	<label for="contributor_first">Contributor's first name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_first_name,
									'id'	=> 'contributor_first_name',
									'name'	=> 'contributor[contributor_first_name]' ) );

		$_form->displayError( 'Contributor', 'contributor_first_name', $__c->contributors()->validationErrors() );
	?>

	<label for="contributor_middle">Contributor's middle name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_middle_name,
									'id'	=> 'contributor_middle_name',
									'name'	=> 'contributor[contributor_middle_name]' ) );
	?>

	<label for="contributor_last">Contributor's last name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_last_name,
									'id'	=> 'contributor_last',
									'name'	=> 'contributor[contributor_last_name]' ) );

		$_form->displayError( 'Contributor', 'contributor_last_name', $__c->contributors()->validationErrors() );
	?>

	
</fieldset>


<fieldset class="formElement">
	<legend>Contributor's Contact Information</legend>
	<label for="contributor_email">Contributor's email address</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_email,
									'id'	=> 'contributor_email',
									'name'	=> 'contributor[contributor_email]' ) );
	?>
	<label for="contributor_contact_consent">Contributor's contact consent</label>
	
	<?php
		$_form->radio(	'contributor[contributor_contact_consent]',
						array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
						'unknown',
						@$contributor->contributor_contact_consent );

		$_form->displayError( 'Contributor', 'contributor_contact_consent', $__c->contributors()->validationErrors() );
	?>
		
		<label for="contributor_phone">Contributor's phone number</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> @$contributor->contributor_phone,
										'id'	=> 'contributor_phone',
										'name'	=> 'contributor[contributor_phone]' ) );
		?>

		<label for="contributor_fax">Contributor's fax number</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> @$contributor->contributor_fax,
										'id'	=> 'contributor_fax',
										'name'	=> 'contributor[contributor_fax]' ) );
		?>
	<label for="contributor_address">Contributor's street address</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '40',
									'value'	=> @$contributor->contributor_address,
									'id'	=> 'contributor_address',
									'name'	=> 'contributor[contributor_address]' ) );
	?>
	<label for="contributor_city">Contributor's city of residence</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_city,
									'id'	=> 'contributor_city',
									'name'	=> 'contributor[contributor_city]' ) );
	?>
	<label for="contributor_state">Contributor's state of residence</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_state,
									'id'	=> 'contributor_state',
									'name'	=> 'contributor[contributor_state]' ) );
	?>

	<label for="contributor_zipcode">Contributor's zipcode of residence</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '10',
									'maxlength'	=> '10',
									'value'	=> @$contributor->contributor_zipcode,
									'id'	=> 'contributor_zipcode',
									'name'	=> 'contributor[contributor_zipcode]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<legend>Contributor's Personal Information</legend>
	<label for="contributor_birth_year">Contributor's year of birth</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '4',
									'maxlength'	=> '4',
									'value'	=> @$contributor->contributor_birth_year,
									'id'	=> 'contributor_birth_year',
									'name'	=> 'contributor[contributor_birth_year]' ) );
	?>

	<label for="contributor_gender">Contributor's gender</label>
	
	<?php
		$_form->select(	array('name' =>'contributor[contributor_gender]' ),
						array( 'unknown' => 'Unknown', 'male' => 'Male', 'female' => 'Female'),
					
						@$contributor->contributor_gender );
	?>

	<label for="contributor_race">Contributor's race</label>
	
	<?php
		$_form->select(	array(	'name'	=> 'contributor[contributor_race]',
								'id'	=> 'contributor_race' ),
						array(	'unknown'					=> 'unknown',
								'Asian/Pacific'				=> 'Asian/Pacific',
						 		'Islander'					=> 'Islander',
								'African American'			=> 'African American',
								'Hispanic'					=> 'Hispanic',
								'Native American / Indian'	=> 'Native American / Indian',
								'White'						=> 'White',
								'Other'						=> 'Other',
								 ),
						@$contributor->contributor_race
					);
	?>
	
	<label for="contributor_race_other">Contributor's race if 'other' is selected above</label>
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_race_other,
									'id'	=> 'contributor_race_other',
									'name'	=> 'contributor[contributor_race_other]' ) );
	?>

	<label for="contributor_occupation">Contributor's occupation</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> @$contributor->contributor_occupation,
									'id'	=> 'contributor_first',
									'name'	=> 'contributor[contributor_occupation]' ) );
	?>
	
	<label for="contributor_residence">Where do you live? (City/State/<abbr title="Postal Code">ZIP</abbr>)</label>
	
	<?php
					$_form->text(	array(	'size'	=> '20',
											'value'	=> $contributor->contributor_residence,
											'id'	=> 'contributor_residence',
											'name'	=> 'contributor[contributor_residence]' ) );
				?>
</fieldset>
<fieldset class="formElement">
	<legend>Contributor's Name and Consent</legend>
	<label for="contributor_first">Contributor's first name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> $contributor->contributor_first_name,
									'id'	=> 'contributor_first_name',
									'name'	=> 'contributor[contributor_first_name]' ) );

		$_form->displayError( 'Contributor', 'contributor_first_name', $__c->contributors()->validationErrors() );
	?>

	<label for="contributor_middle">Contributor's middle name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> $contributor->contributor_middle_name,
									'id'	=> 'contributor_middle_name',
									'name'	=> 'contributor[contributor_middle_name]' ) );
	?>

	<label for="contributor_last">Contributor's last name</label>
	
	<?php
		$_form->text( array(	'size'	=> '20',
									'value'	=> $contributor->contributor_last_name,
									'id'	=> 'contributor_last',
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
						$contributor->contributor_contact_consent );

		$_form->displayError( 'Contributor', 'contributor_contact_consent', $__c->contributors()->validationErrors() );
	?>
		<label for="contributor_email">Contributor's email address</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> $contributor->contributor_email,
										'id'	=> 'contributor_email',
										'name'	=> 'contributor[contributor_email]' ) );
		?>
		<label for="contributor_phone">Contributor's phone number</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> $contributor->contributor_phone,
										'id'	=> 'contributor_phone',
										'name'	=> 'contributor[contributor_phone]' ) );
		?>

		<label for="contributor_fax">Contributor's fax number</label>
		
		<?php
			$_form->text(
								array(	'size'	=> '20',
										'value'	=> $contributor->contributor_fax,
										'id'	=> 'contributor_fax',
										'name'	=> 'contributor[contributor_fax]' ) );
		?>
	<label for="contributor_address">Contributor's street address</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '40',
									'value'	=> $contributor->contributor_address,
									'id'	=> 'contributor_address',
									'name'	=> 'contributor[contributor_address]' ) );
	?>
	<label for="contributor_city">Contributor's city of residence</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> $contributor->contributor_city,
									'id'	=> 'contributor_city',
									'name'	=> 'contributor[contributor_city]' ) );
	?>
	<label for="contributor_state">Contributor's state of residence</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> $contributor->contributor_state,
									'id'	=> 'contributor_state',
									'name'	=> 'contributor[contributor_state]' ) );
	?>

	<label for="contributor_zipcode">Contributor's zipcode of residence</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '10',
									'maxlength'	=> '10',
									'value'	=> $contributor->contributor_zipcode,
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
									'value'	=> $contributor->contributor_birth_year,
									'id'	=> 'contributor_birth_year',
									'name'	=> 'contributor[contributor_birth_year]' ) );
	?>

	<label for="contributor_gender">Contributor's gender</label>
	
	<?php
		$_form->select(	array('name' =>'contributor[contributor_gender]' ),
						array( 'male' => 'Male', 'female' => 'Female', 'unknown' => 'Unknown' ),
					
						$contributor->contributor_gender );
	?>

	<label for="contributor_race">Contributor's race</label>
	
	<?php
		$_form->select(	array(	'name'	=> 'contributor[contributor_race]',
								'id'	=> 'contributor_race' ),
						array(	'Asian/Pacific'				=> 'Asian/Pacific',
						 		'Islander'					=> 'Islander',
								'African American'			=> 'African American',
								'Hispanic'					=> 'Hispanic',
								'Native American / Indian'	=> 'Native American / Indian',
								'White'						=> 'White',
								'Other'						=> 'Other',
								'unknown'					=> 'unknown' ),
						$contributor->contributor_race
					);
	?>
	
	<label for="contributor_race_other">Contributor's race if 'other' is selected above</label>
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> $contributor->contributor_race_other,
									'id'	=> 'contributor_race_other',
									'name'	=> 'contributor[contributor_race_other]' ) );
	?>

	<label for="contributor_occupation">Contributor's occupation</label>
	
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> $contributor->contributor_occupation,
									'id'	=> 'contributor_first',
									'name'	=> 'contributor[contributor_occupation]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_jewish">Does the contributor identify themselves as Jewish?</label>
	
	<?php
		$_form->radio(	'contributor[contributor_jewish]',
						array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
						null,
						$contributor->contributor_jewish );
	?>

<label for="contributor_religious_id">Contributor's religious identification</label>
	
	<?php
		$_form->select(	array(	'name'	=> 'contributor[contributor_religious_id]',
								'id'	=> 'contributor_religious_id' ),
						array(	'reform'		=> 'Jewish / Reform',
						 		'conservative'	=> 'Jewish / Conservative',
								'orthodox'		=> 'Jewish / Orthodox',
								'secular'		=> 'Jewish / Secular',
								'protestant'	=> 'Protestant',
								'catholic'		=> 'Catholic',
								'muslim'		=> 'Muslim',
								'none'			=> 'No Religious Affiliation',
								'other'			=> 'Other',
								'unknown' 		=> 'Unknown'),
						$contributor->contributor_religious_id
					);
	?>
	
	<label for="contributor_religious_id_other">Other:</label>
	<?php
		$_form->text(	array(	'size'	=> '20',
								'value'	=> $contributor->contributor_religious_id_other,
								'id'	=> 'contributor_religious_id_other',
								'name'	=> 'contributor[contributor_religious_id_other]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label>Is the contributor a member of the New Orleans or Gulf Coast communities?</label>
	<label class="radiolabel">
		<input type="radio" id="nolaY" name="is_nola" onclick="document.getElementById('not-nola').style.display = 'none'; new Effect.BlindDown( 'is-nola', {duration: 0.8} );" <?php if( isset( $saved['is_nola'] ) && $saved['is_nola'] == 'on' ){ echo ' checked="checked" '; } ?>/>Yes</label>
		<label class="radiolabel"><input type="radio" id="nolaN" name="is_nola" onclick="document.getElementById('is-nola').style.display = 'none'; new Effect.BlindDown( 'not-nola', {duration: 0.8} );" <?php if( isset( $saved['is_nola'] ) && $saved['is_nola'] == 'on' ){ echo ' checked="checked" '; } ?> />No</label>
	
	<fieldset class="formElement" id="is-nola" style="display:none;">
		<fieldset class="formElement">
			<label for="contributor_location_during">Where did the contributor live at the time of the hurricane?</label>
			
			<?php
				$_form->textarea( 	array(	'rows'	=> '4',
											'cols'	=> '60',
											'id'	=> 'contributor_location_during',
											'name'	=> 'contributor[contributor_location_during]' ),
											$contributor->contributor_location_during );
			?>
		</fieldset>

		<fieldset class="formElement">
			<label for="contributor_location_evacuation">To where did the contributor evaculate at the time of the hurricane?</label>
			<?php
				$_form->textarea( 	array(	'rows'	=> '4',
											'cols'	=> '60',
											'id'	=> 'contributor_location_evacuation',
											'name'	=> 'contributor[contributor_location_evacuation]' ),
											$contributor->contributor_location_evacuation );
			?>
		</fieldset>

		<fieldset class="formElement">
			<label for="contributor_location_between">Where did the contributor live in between? <em>Separate multiple locations with semi-colons</em></label>
			
			<?php
			$_form->textarea( 	array(	'rows'	=> '4',
										'cols'	=> '60',
										'id'	=> 'contributor_location_between',
										'name'	=> 'contributor[contributor_location_between]' ),
										$contributor->contributor_location_between );
			?>
		</fieldset>

		<fieldset class="formElement">
			<label for="contributor_return">Does the contributor plan to return to the Gulf Coast?</label>
			
			<?php
				$_form->radio(	'contributor[contributor_return]',
								array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
								null,
								$contributor->contributor_return );
			?>
		</fieldset>
	</fieldset>

	<fieldset id="not-nola" style="display:none;">
		<fieldset class="formElement">
			<label for="contributor_family_members">Were the contributor's family or friends affected by Katrina?</label>
			
			<?php
				$_form->radio(	'contributor[contributor_family_members]',
								array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
								null,
								$contributor->contributor_family_members );
			?>
		</fieldset>

		<fieldset class="formElement">
			<label for="contributor_former_resident">Was the contributor a former resident of New Orleans?</label>
			
			<?php
				$_form->radio(	'contributor[contributor_former_resident]',
								array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
								null,
								$contributor->contributor_former_resident );
			?>
		</fieldset>

		<fieldset class="formElement">
			<label for="contributor_community_evacuees">Did the contributor's community take in evacuees?</label>
			
			<?php
				$_form->radio(	'contributor[contributor_community_evacuees]',
								array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
								null,
								$contributor->contributor_community_evacuees );
			?>
		</fieldset>

		<fieldset class="formElement">
			<label for="contributor_participate">Did the contributor participate in direct relief efforts?</label>
			
			<?php
				$_form->radio(	'contributor[contributor_participate]',
								array( 'yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown' ),
								null,
								$contributor->contributor_participate );
			?>
			<div id="reliefefforts-yes">
			<label for="contributor_location_participate">Location where the contributor participated in relief efforts</label>
			
			<?php
				$_form->textarea(
									array(	'rows'	=> '4',
											'cols'	=> '60',
											'id'	=> 'contributor_location_participate',
											'name'	=> 'contributor[contributor_location_participate]' ),
											$contributor->contributor_location_participate );
			?>
			</div>
		</fieldset>

		<fieldset class="formElement">
			<label for="contributor_other_relationship">If the contributor has some other relationship to the events surrounding Hurricane Katrina, detail them here:</label>
			
			<?php
				$_form->textarea( 	array(	'rows'	=> '4',
											'cols'	=> '60',
											'id'	=> 'contributor_other_relationship',
											'name'	=> 'contributor[contributor_other_relationship]' ),
											$contributor->contributor_other_relationship );
			?>
		</fieldset>
	</fieldset>
</fieldset>
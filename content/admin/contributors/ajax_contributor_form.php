<h1>Object Contributor</h1>

<fieldset class="formElement">
	<label for="contributor_first">Contributor's first name</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_first',
									'name'	=> 'contributor[contributor_first_name]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_middle">Contributor's middle name</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_middle',
									'name'	=> 'contributor[contributor_middle_name]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_last">Contributor's last name</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_last',
									'name'	=> 'contributor[contributor_last_name]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_full">Contributor's full name</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_full',
									'name'	=> 'contributor[contributor_full_name]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_contact_consent">Contributor's contact consent</label>
	<br/>
	<select name="contributor[contributor_contact_consent]" id="contributor_contact_consent">
		<option value="">Select Below</option>
		<option value="yes">Yes</option>
		<option value="no">No</option>
		<option value="unknown">Unknown</option>
	</select>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_email">Contributor's email address</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_email',
									'name'	=> 'contributor[contributor_email]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_phone">Contributor's phone number</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_phone',
									'name'	=> 'contributor[contributor_phone]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_fax">Contributor's fax number</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_fax',
									'name'	=> 'contributor[contributor_fax]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_address">Contributor's street address</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_address',
									'name'	=> 'contributor[contributor_address]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_city">Contributor's city of residence</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_city',
									'name'	=> 'contributor[contributor_city]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_state">Contributor's state of residence</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_state',
									'name'	=> 'contributor[contributor_state]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_zipcode">Contributor's zipcode of residence</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_zipcode',
									'name'	=> 'contributor[contributor_zipcode]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_birth_year">Contributor's year of birth</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_birth_year',
									'name'	=> 'contributor[contributor_birth_year]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_gender">Contributor's gender</label>
	<br/>
	<select name="contributor[contributor_gender]" id="contributor_gender">
		<option value="">Select Below</option>
		<option value="male">Male</option>
		<option value="female">Female</option>
		<option value="unknown">Unknown</option>
	</select>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_race">Contributor's race</label>
	<br/>
	<select name="contributor[contributor_race]" id="contributor_race">
		<option value="">Select Below</option>
		<option value="Asian/Pacific">Asian / Pacific</option>
		<option value="Islander">Islander</option>
		<option value="Black">Black</option>
		<option value="Hispanic">Hispanic</option>
		<option value="Native American / Indian">Native American / Indian</option>
		<option value="White">White</option>
		<option value="Other">Other ( describe below )</option>
		<option value="unknown">Unknown</option>
	</select>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_race_other">Contributor's race if 'other' is selected above</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_race_other',
									'name'	=> 'contributor[contributor_race_other]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_occupation">Contributor's occupation</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_first',
									'name'	=> 'contributor[contributor_occupation]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_jewish">Indication if the contributor is Jewish or not</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_jewish',
									'name'	=> 'contributor[contributor_jewish]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_identification">Contributor's religious identification</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_identification',
									'name'	=> 'contributor[contributor_identification]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_location_during">Contributor's location during Hurricane Katrina</label>
	<br/>
	<?php
		$form->textarea( 	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'contributor_location_during',
									'name'	=> 'contributor[contributor_location_during]' ),
									null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_location_evacuation">Contributor's location to which they were evacuated</label>
	<?php
		$form->textarea( 	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'contributor_location_evacuation',
									'name'	=> 'contributor[contributor_location_evacuation]' ),
									null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_location_current">Contributor's current location</label>
	<br/>
	<?php
	$form->textarea( 	array(	'rows'	=> '4',
								'cols'	=> '60',
								'id'	=> 'contributor_location_current',
								'name'	=> 'contributor[contributor_location_current]' ),
								null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_location_between">Contributor's location(s) of displacement</label>
	<?php
	$form->textarea( 	array(	'rows'	=> '4',
								'cols'	=> '60',
								'id'	=> 'contributor_location_between',
								'name'	=> 'contributor[contributor_location_between]' ),
								null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_return">Contributor's intent to return to the Gulf Coast</label>
	<?php
	$form->textarea( 	array(	'rows'	=> '4',
								'cols'	=> '60',
								'id'	=> 'contributor_return',
								'name'	=> 'contributor[contributor_return]' ),
								null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_family_members">Contributor's family or friends affected by Katrina</label>
	<?php
	$form->textarea( 	array(	'rows'	=> '4',
								'cols'	=> '60',
								'id'	=> 'contributor_family_members',
								'name'	=> 'contributor[contributor_family_members]' ),
								null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_former_resident">Was the contributor a former resident of New Orleans?</label>
	<br/>
	<?php
		$form->textarea( 	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'contributor_former_resident',
									'name'	=> 'contributor[contributor_former_resident]' ),
									null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_community_evacuees">Did the contributor's community take in evacuees?</label>
	<br/>
	<?php
	$form->textarea( 	array(	'rows'	=> '4',
								'cols'	=> '60',
								'id'	=> 'contributor_community_evacuees',
								'name'	=> 'contributor[contributor_community_evacuees]' ),
								null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_participate">Indication of whether the contributor took part in direct relief efforts</label>
	<?php
		$form->textarea( 	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'contributor_participate',
									'name'	=> 'contributor[contributor_participate]' ),
									null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_other_relationship">Any other relationships to Hurricane Katrina</label>
	<?php
		$form->textarea( 	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'contributor_other_relationship',
									'name'	=> 'contributor[contributor_other_relationship]' ),
									null );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_residence">Contributor's current residence</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_residence',
									'name'	=> 'contributor[contributor_residence]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="contributor_location_participate">Location where the contributor participated in relief efforts</label>
	<br/>
	<?php
		$form->input( 'text',
							array(	'size'	=> '20',
									'value'	=> null,
									'id'	=> 'contributor_location_participate',
									'name'	=> 'contributor[contributor_location_participate]' ) );
	?>
</fieldset>
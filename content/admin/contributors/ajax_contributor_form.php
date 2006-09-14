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
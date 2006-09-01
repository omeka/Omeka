<?php
if( self::$_request->getProperty( 'contribute_submit' ) )
{
	$saved = self::$_request->getProperties();	
}
else
{
	$saved = false;
}
?>
<label>Where did you live at the time of the hurricane?</label>
<?php
	$_form->text( array('class'	=> 'textinput',
						'name'	=> 'Contributor[contributor_location_during]',
						'value'	=> $saved['Contributor']['contributor_location_during'] ) );
?> <em>city/state/zip</em>

<label>Where did you evacuate to?</label>
<?php
	$_form->text( array('class'	=> 'textinput',
						'name'	=> 'Contributor[contributor_location_evacuation]',
						'value'	=> $saved['Contributor']['contributor_location_evacuation'] ) );
?> <em>city/state/zip</em>

<label>Where do you live now?</label>
<?php
	$_form->text( array('class'	=> 'textinput',
						'name'	=> 'Contributor[contributor_location_current]',
						'value'	=> $saved['Contributor']['contributor_location_current'] ) );
?> <em>city/state/zip</em>

<label>Where did you live in between? (Separate multiple locations with semi-colons.)</label>
<?php
	$_form->text( array('class'	=> 'textinput',
						'name'	=> 'Contributor[contributor_location_between]',
						'value'	=> $saved['Contributor']['contributor_location_between'] ) );
?> <em>city/state/zip</em>

<label>Do you plan to return to New Orleans or the Gulf Coast?</label>
<?php
	$_form->radio( 'Contributor[contributor_return]',
					array(	'yes'	=> 'Yes',
							'no'	=> 'No' ),
					'yes',
					$saved['Contributor']['contributor_return']	);
?>
<br/>
<br/>
<input type="submit" class="input-submit" value="Enter your Contribution -&gt;" name="contribute_submit" onclick="return confirm('Are you sure you would like to submit your contribution now?');" />
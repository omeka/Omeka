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
<label>What describes your relationship to Hurricane Katrina?(Check all that apply)</label>
<label class="label-checkbox"><input class="label-checkbox" type="checkbox" name="Contributor[contributor_family_members]" value="yes" <?php if( isset( $saved['Contributor']['contributor_family_members'] ) && $saved['Contributor']['contributor_family_members'] == 'on' ){ echo ' checked="checked" '; }?> />I have family members and/or friends who were directly affected</label>

<label class="label-checkbox"><input class="checkbox" type="checkbox" name="Contributor[contributor_former_resident]" value="yes" <?php if( isset( $saved['Contributor']['contributor_former_resident'] ) && $saved['Contributor']['contributor_former_resident'] == 'on' ){ echo ' checked="checked" '; }?>/>I am a former resident of New Orleans or the Gulf Coast</label>

<label class="label-checkbox"><input class="checkbox" type="checkbox" name="Contributor[contributor_community_evacuees]" value="yes" <?php if( isset( $saved['Contributor']['contributor_community_evacuees'] ) && $saved['Contributor']['contributor_community_evacuees'] == 'on' ){ echo ' checked="checked" '; }?> />My community took in evacuees</label>

<label class="label-checkbox"><input class="checkbox" type="checkbox" name="Contributor[contributor_participate]" value="yes" <?php if( isset( $saved['Contributor']['contributor_participate'] ) && $saved['Contributor']['contributor_participate'] == 'on' ){ echo ' checked="checked" '; }?>/>I participated in direct relief efforts</label>

<label class="label-checkbox" for="contributor_other_relationship">If you have some other relationship to the events surrounding Hurricane Katrina, detail them here:</label>
<?php
	$_form->textarea( array('cols'	=> 30,
							'rows'	=> 4,
							'id'	=> 'contributor_other_relationship',
							'name'	=> 'Contributor[contributor_other_relationship]' ),
							$saved['Contributor']['contributor_other_relationship'] );
?>

<label>Where do you live?</label>
<?php
	$_form->text( array('class'	=> 'textinput',
						'name'	=> 'Contributor[contributor_residence]',
						'value'	=> $saved['Contributor']['contributor_residence'] ) );
?>
<label>Where did you participate in relief efforts? (separate multiple locations by semi-colon)</label>
<?php
	$_form->text( array('class'	=> 'textinput',
						'name'	=> 'Contributor[contributor_location_participate]',
						'value'	=> $saved['Contributor']['contributor_location_participate'] ) );
?>
<br/>
<br/>
<input type="submit" class="input-submit" value="Enter your Contribution -&gt;" name="contribute_submit" onclick="return confirm('Are you sure you would like to submit your contribution now?');" />
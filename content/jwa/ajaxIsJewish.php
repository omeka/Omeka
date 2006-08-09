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
<label for="contributor_religious_id">How do you identify yourself?</label>
<?php
	$_form->select(	array(	'name'	=> 'Contributor[contributor_religious_id]',
							'id'	=> 'contributor_religious_id' ),
					array(	'reform'		=> 'Reform',
							'conservative'	=> 'Conservative',
							'orthodox'		=> 'Orthodox',
							'reconstructionist'		=> 'Reconstructionist',
							'non-denominational'		=> 'Non-denominational',
							'secular / cultural'		=> 'Secular / Cultural',
							'other'			=> 'Other' ),
					$saved['Contributor']['contributor_religious_id'] );
?>
<label for="contributor_religious_id_other">if other:</label>
<?php
	$_form->text( array('id'	=> 'contributor_religious_id_other',
						'name'	=> 'Contributor[contributor_religious_id_other]',
						'class'	=> 'textinput',
						'size'	=> 20,
						'value'	=> $saved['Contributor']['contributor_religious_id_other'] ) );
?>
<input type="hidden" name="isJewishFlag"/>
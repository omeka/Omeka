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
<label for="contributor_religious_id">Contributor's religious identification</label>
	<br/>
	<?php
		$_form->select(	array(	'name'	=> 'contributor[contributor_religious_id]',
								'id'	=> 'contributor_religious_id' ),
						array(	'protestant'	=> 'Protestant',
								'catholic'		=> 'Catholic',
								'muslim'		=> 'Muslim',
								'none'			=> 'No Religious Affiliation',
								'other'			=> 'Other',
								'unknown' 		=> 'Unknown'),
						$contributor->contributor_religious_id
					);
	?>
	<br/>
	<label for="contributor_religious_id_other">Other:</label>
	<?php
		$_form->text(	array(	'size'	=> '20',
								'value'	=> $contributor->contributor_religious_id_other,
								'id'	=> 'contributor_religious_id_other',
								'name'	=> 'contributor[contributor_religious_id_other]' ) );
	?>
<input type="hidden" name="notJewishFlag"/>
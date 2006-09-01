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
<fieldset id="section4">
	<legend><span class="number">4</span> Provide more information about yourself</legend>
	<label>In what year were you born?</label>
	<select name="Contributor[contributor_birth_year]">
		<option value="" selected>Select Below</option>
		<?php
			for( $g = date( 'Y', time() ); $g > ( date( 'Y', time() ) - 100 ); $g-- )
			{
				$birthyear = '';
				$birthyear .= '<option';
				if( $saved['Contributor']['contributor_birth_year'] == $g )
				{
					$birthyear .= ' selected ';
				}
				$birthyear .= '>' . $g . '&nbsp;</option>' . "\n";
				echo $birthyear;
			}
		?>
	</select>

	<label>What is your gender?</label>
	<?php
		$_form->select(	array(	'name'	=> 'Contributor[contributor_gender]',
								'id'	=> 'contributor_gender' ),
						array(	'male'	=> 'Male',
								'female'=> 'Female' ),
						$saved['Contributor']['contributor_gender'] );
	?>

	<label for="contributor_race">What is your race?</label>	
	<?php
		$_form->select(	array(	'name'	=> 'Contributor[contributor_race]',
								'id'	=> 'contributor_race' ),
						array(	'Asian / Pacific Islander'	=> 'Asian / Pacific Islander',
								'Black'						=> 'Black',
								'Hispanic'					=> 'Hispanic',
								'Native / American Indian'	=> 'Native / American Indian',
								'White'						=> 'White',
								'Other'						=> 'Other' ),
						$saved['Contributor']['contributor_race']
					);
	?>
	
	<label for="contributor_race_other"><em>If you selected "other," what is your race?</em></label>
	<?php
		$_form->text(
							array(	'size'	=> '20',
									'value'	=> $saved['Contributor']['contributor_race_other'],
									'id'	=> 'contributor_race_other',
									'name'	=> 'Contributor[contributor_race_other]' ) );
	?>

	<label for="contributor_occupation">What is your occupation?</label>
	<?php 
		$_form->text( array(	'id'	=> 'contributor_occupation',
								'name'	=> 'Contributor[contributor_occupation]',
								'class'	=> 'textinput',
								'size'	=> 30,
								'value'	=> $saved['Contributor']['contributor_occupation'] ) );
	?>
	
	<p>Are you Jewish?</p>
	<label class="radiolabel"><input type="radio" id="jewishY" name="Contributor[contributor_jewish]" value="yes" onclick="revealSwitch( 'jewish', 'ajaxIsJewish');" <?php if( isset( $saved['Contributor']['contributor_jewish'] ) && $saved['Contributor']['contributor_jewish'] == 'yes' ){ echo ' checked="checked" ';} ?>/>Yes</label>
	<label class="radiolabel"><input type="radio" id="jewishN" name="Contributor[contributor_jewish]" value="no" onclick="revealSwitch( 'jewish', 'ajaxNotJewish');" <?php if( isset( $saved['Contributor']['contributor_jewish'] ) && $saved['Contributor']['contributor_jewish'] == 'no' ){ echo ' checked="checked" ';} ?>/>No</label>
</fieldset>

<!-- Ajax content goes here -->
<div id="jewish">
	<?php
		if( isset( $saved['isJewishFlag'] ) )
		{
			include( 'ajaxIsJewish.php' );
		}
		elseif( isset( $saved['notJewishFlag'] ) )
		{
			include( 'ajaxNotJewish.php');
		}
	?>
</div>
	<p>Are you a member of the New Orleans or Gulf Coast communities?</p>
<label class="radiolabel">
	<input type="radio" id="nolaY" name="is_nola" onclick="revealSwitch( 'nola', 'ajaxIsNola');" <?php if( isset( $saved['is_nola'] ) && $saved['is_nola'] == 'on' ){ echo ' checked="checked" '; } ?>/>Yes</label>
	<label class="radiolabel"><input type="radio" id="nolaN" name="is_nola" onclick="revealSwitch( 'nola', 'ajaxNotNola');" <?php if( isset( $saved['is_nola'] ) && $saved['is_nola'] == 'on' ){ echo ' checked="checked" '; } ?> />No</label>

<div id="nola">
	<?php
		if( isset( $saved['Contributor']['contributor_location_during'] ) )
		{
			include( 'ajaxIsNola.php' );
		}
		elseif( isset( $saved['Contributor']['contributor_location_participate'] ) )
		{
			include( 'ajaxNotNola.php' );
		}
	?>
</div>
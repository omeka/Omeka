<fieldset id="section4">
	<legend><span class="number">4</span> Provide more information about yourself</legend>
	<label>In what year were you born?</label>
	<select name="Contributor[contributor_birth_year]">
		<option value="">Undisclosed</option>
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
						array(	''		=> 'Undisclosed',
								'male'	=> 'Male',
								'female'=> 'Female' ),
						$saved['Contributor']['contributor_gender'] );
	?>

	<label for="contributor_occupation">What is your occupation?</label>
	<?php 
		$_form->text( array(	'id'	=> 'contributor_occupation',
								'name'	=> 'Contributor[contributor_occupation]',
								'class'	=> 'textinput',
								'size'	=> 30,
								'value'	=> $saved['Contributor']['contributor_occupation'] ) );
	?>
</div>

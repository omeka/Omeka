<fieldset>
	<label for="user_name">Name:</label><br/>
<?php
	$_form->text( array(	'id'	=> 'user_name',
	 						'name'	=> 'user[user_username]',
	 						'value'	=> $user->user_username,
	 						'size' => '23' ) );
?>
</fieldset>

<fieldset>
	<label for="user_first_name">First Name:</label><br/>
<?php
	$_form->text( array(	'id'	=> 'user_first_name',
	 						'name'	=> 'user[user_first_name]',
	 						'value'	=> $user->user_first_name,
	 						'size' => '23' ) );
?>
</fieldset>

<fieldset>
	<label for="user_last_name">Last Name:</label><br/>
<?php
	$_form->text( array(	'id'	=> 'user_last_name',
	 						'name'	=> 'user[user_last_name]',
	 						'value'	=> $user->user_last_name,
	 						'size' => '23' ) );
?>
</fieldset>

<fieldset>
	<label for="user_institution">Institution:</label><br/>
<?php
	$_form->text( array(	'id'	=> 'user_institution',
	 						'name'	=> 'user[user_institution]',
	 						'value'	=> $user->user_institution,
	 						'size' => '23' ) );
?>
</fieldset>

<fieldset>
	<label for="user_email">Email:</label><br/>
<?php
	$_form->text( array(	'id'	=> 'user_email',
	 						'name'	=> 'user[user_email]',
	 						'value'	=> $user->user_email,
	 						'size' => '23' ) );
?>
</fieldset>



<fieldset class="formElement">
	<label for="user_permission_id">Permission Level</label>
	<?php
		$_form->select(	array(	'name'	=> 'user[user_permission_id]',
								'id'	=> 'user_permission_id' ),
						array(	'50'	=> 'Public',
								'20'	=> 'Researcher',
								'10'	=> 'Admin',
								'1'	=> 'Superuser' ),
						$user->user_permission_id	);
	?>
	
	
</fieldset>
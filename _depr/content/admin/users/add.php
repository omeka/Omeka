<?php include( 'subnav.php' ); ?>
<?php
//Layout: default;
$user = $__c->users()->add();
?>
<h2>Add a User</h2>
<?php
// Print confirmation of user creation
if ($user==false): ?>
	<h3>User created</h3>
	<p>User has been created, and account information was sent to the e-mail address specified. Click <a href="<?php echo $_link->to( 'users', 'all' ); ?>">here</a> to return to the main user list.</p>

<?php else: ?>
<form method="post" action="<?php echo $_link->to( 'users', 'add' ); ?>">
	
	<?php if( $msg = self::$_session->flash() ):?>
	<h3 id="error"><?php echo $msg; ?></h3>
	<?php endif; ?>
	
	<fieldset>
		<label for="user_first_name">First Name:</label>
		<?php
			$_form->text( array('id'	=> 'user_first_name',
								'class' => 'textinput',
								'name'	=> 'user[user_first_name]',
								'value'	=> $user->user_first_name ) );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_last_name">Last Name:</label>
		<?php
			$_form->text( array('id'	=> 'user_last_name',
								'class' => 'textinput',
								'name'	=> 'user[user_last_name]',
								'value'	=> $user->user_last_name ) );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_name">Username:</label>
		<?php
			$_form->text( array('name' 	=> 'user[user_username]',
			 					'id'	=> 'user_name',
								'class' => 'textinput',
			 					'value'	=> $user->user_username ) );
			$_form->displayError( 'User', 'user_username', $__c->users()->validationErrors() );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_email">Email address:</label>
		<?php
			$_form->text( array('id'	=> 'user_email',
								'name'	=> 'user[user_email]',
								'class' => 'textinput',
								'value'	=> $user->user_email ) );
			$_form->displayError( 'User', 'user_email', $__c->users()->validationErrors() );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_institution">User's affiliated institution:</label>
		<?php
			$_form->text( array('id'	=> 'user_institution',
								'name'	=> 'user[user_institution]',
								'class' => 'textinput',
								'value'	=> $user->user_institution ) );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_permission_id">User's permission level</label>
		<select class="selectinput" name="user[user_permission_id]" id="user_permission_id">
			<option value="">Select a Permission Level</option>
			<?php if( self::$_session->getUser()->getPermissions() == 1 ): ?>
			<option value="1">Super User</option>
			<?php endif; ?>
			<option value="10">Administrator</option>
			<option value="20">Researcher</option>
			<option value="30">Public User</option>
		</select>
		<?php
			$_form->displayError( 'User', 'user_permission_id', $__c->users()->validationErrors() );
		?>
	</fieldset>
	
	<p class="instruction-text">The user's password will be emailed to them at the address input above.</p>
	<input type="submit" class="submitinput" name="user_add" value="Create New User -&gt;"/>
</form>
<?php endif; ?>
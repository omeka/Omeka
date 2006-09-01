<?php include( 'subnav.php' ); ?>
<?php
//Layout: default;
$user = $__c->users()->add();

// Print confirmation of user creation
if ($user==false): ?>
	<h3>User created</h3>
	<p>User has been created, and account information was sent to the e-mail address specified. Click <a href="<?php echo $_link->to( 'users', 'all' ); ?>">here</a> to return to the main user list.</p>

<?php else: ?>
<br/>
<form method="post" action="<?php echo $_link->to( 'users', 'add' ); ?>">
	
	<?php if( $msg = self::$_session->flash() ):?>
	<h2 id="error"><?php echo $msg; ?></h2>
	<?php endif; ?>
	
	<fieldset>
		<label for="user_first_name">First Name:</label><br/>
		<?php
			$_form->text( array('id'	=> 'user_first_name',
								'name'	=> 'user[user_first_name]',
								'value'	=> $user->user_first_name ) );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_last_name">Last Name:</label><br/>
		<?php
			$_form->text( array('id'	=> 'user_last_name',
								'name'	=> 'user[user_last_name]',
								'value'	=> $user->user_last_name ) );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_name">Username:</label><br/>
		<?php
			$_form->text( array('name' 	=> 'user[user_username]',
			 					'id'	=> 'user_name',
			 					'value'	=> $user->user_username ) );
			$_form->displayError( 'User', 'user_username', $__c->users()->validationErrors() );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_email">Email address:</label><br/>
		<?php
			$_form->text( array('id'	=> 'user_email',
								'name'	=> 'user[user_email]',
								'value'	=> $user->user_email ) );
			$_form->displayError( 'User', 'user_email', $__c->users()->validationErrors() );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_institution">User's affiliated institution:</label><br/>
		<?php
			$_form->text( array('id'	=> 'user_institution',
								'name'	=> 'user[user_institution]',
								'value'	=> $user->user_institution ) );
		?>
	</fieldset>
	
	<fieldset>
		<label for="user_permission_id">User's permission level</label><br/>
		<select name="user[user_permission_id]" id="user_permission_id">
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
	
	<p class="instructionText">The user's password will be emailed to them at the address input above.</p>
	<input type="submit" name="user_add" value="Create New User -&gt;"/>
</form>
<?php endif; ?>
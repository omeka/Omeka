<?php 
// Layout: default;
	$__c->users()->edit(); 
	echo self::$_session->flash();
?>
		<h2>MyArchive</h2>
		<?php include( $_partial->file( 'secondarynav' ) ); ?>
		<div id="myprofile">
				<form method="post" action="">
					
					<fieldset>
						<legend>Personal Information</legend>
						
					<label for="user_first_name">First Name</label>
					<?php
						$_form->text( array(	'name'		=> 'user_first_name',
												'class'		=> 'textinput',
												'value'		=>	self::$_session->getUser()->user_first_name ) );
					?><br />

					<label for="user_last_name">Last Name</label>
					<?php
						$_form->text( array(	'name'		=> 'user_last_name',
												'class'		=> 'textinput',
												'value'		=>	self::$_session->getUser()->user_last_name ) );
					?><br />

					<label for="user_institution">Institution</label>
					<?php
						$_form->text( array(	'name'		=> 'user_institution',
												'class'		=> 'textinput',
												'value'		=>	self::$_session->getUser()->user_institution ) );
					?>
					</fieldset>

					<fieldset id="changepassword">
					<legend>Change My Password</legend>
					<p>Leave blank to keep password the same</p>
					<label for="old_password">Old Password</label>
					<input name="old_password" id="old_password" class="textinput" type="password" /><br />
					<label for="new_password">New Password (enter twice)</label>
					<input name="new_password_1" class="textinput" id="new_password_1" type="password" />
					<input name="new_password_2" class="textinput" id="new_password_2" type="password" />
					<?php 
						$_form->hidden( array( 'name'   => 'user_id',
												'id'    => 'user_id',
												'value' => self::$_session->getUser()->getId() ) );
					?>
					</fieldset>
					
					<input type="submit" name="user_edit" value="Save Changes" id="user_edit" />
					</form>
		</div>	


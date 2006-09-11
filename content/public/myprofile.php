<?php 
// Layout: default;

	$file = $__c->users()->edit(); 
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
					<label for="oldPassword">Old Password</label>
					<input name="oldPassword" id="oldPassword" class="textinput" type="password" /><br />
					<label for="newPassword">New Password</label>
					<input name="newPassword" class="textinput" id="newPassword" type="password" />
					</fieldset>
					
					<input type="submit" name="user_edit" value="Save Changes" id="user_edit" />
					</form>
		</div>	


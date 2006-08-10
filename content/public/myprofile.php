<?php 
	$file = $__c->users()->edit(); 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>My Archive | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

</head>

<body id="myarchive" class="myprofile">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>MyArchive</h2>
		<?php include ('inc/secondarynav.php')?>
		<div id="primary">
			<h3>MyProfile</h3>
				
				<form method="post" action="">
					<fieldset>
						<legend>Personal Information</legend>
						
					<label for="user_first_name">First Name</label>
					<?php
						$_form->text( array(	'name'		=> 'user_first_name',
												'class'		=> 'textinput',
												'value'		=>	self::$_session->getUser()->user_first_name ) );
					?>
					</fieldset>

					<fieldset>
					<label for="user_last_name">Last Name</label>
					<?php
						$_form->text( array(	'name'		=> 'user_last_name',
												'class'		=> 'textinput',
												'value'		=>	self::$_session->getUser()->user_last_name ) );
					?>
					</fieldset>

					<fieldset>
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
					<input name="oldPassword" id="oldPassword" class="textinput" type="password" />
					<label for="newPassword">New Password</label>
					<input name="newPassword" class="textinput" id="newPassword" type="password" />
					</fieldset>
					
					<input type="submit" name="user_edit" value="Save Changes" id="user_edit" />
					</form>
		</div>	
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>




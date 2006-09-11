<?php
//Layout: default;
$data = $__c->users()->changePassword();
?>
<?php include( 'subnav.php' ); ?>

<style type="text/css" media="screen">
/* <![CDATA[ */
	#my-account-edit input {margin: 4px 0;}
	#my-account-edit label {display:block;}
/* ]]> */
</style>

<div class="container">
<div id="my-account-edit">
<?php $msg = self::$_session->flash();?>
<?php if (!@$data->user_id && !$msg): ?>
<p>This password has been changed, and the user has been notified by e-mail.</p>
<p>Please click <a href="<?php echo $_link->to( 'users' ); ?>">here</a> to return.</p>
<?php else: ?>
<?php if($msg): echo $msg; endif;?>
<h2>Changing password for <?php if ($data->user_first_name) echo $data->user_first_name.' '.$data->user_last_name; ?></h2><br />
<form method="post" action="<?php echo $_link->to( 'users', 'password' ); ?>">
	<?php if (!self::$_session->isSuper()):?>
	<label for="old_password">Old Password:</label>
	<input type="password" name="old_password" id="old_password" /><br/>
	<?php endif; ?>
	<label for="new_password_1">New Password:</label>
	<input type="password" name="new_password_1" id="new_password_1" /><br/>
	<label for="new_password_2">Repeat New Password:</label>
	<input type="password" name="new_password_2" id="new_password_2" /><br/>
	<input type="hidden" name="user_id" id="user_id" value="<?php echo $data->user_id;?>">
	<input type="submit" value="Change Password" name="change_password"/>
</form>
	<?php endif; ?>
</div>
</div>
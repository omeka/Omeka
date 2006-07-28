<?php
//Layout: default;
$e = $__c->accounts()->edit();
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
<?php if ($e): ?>
<p>Your password has been changed</p>
<p>Please click <a href="<?php echo $_link->to( 'account' ); ?>">here</a> to return.</p>
<?php else: ?>

<form method="post" action="<?php echo $_link->to( 'account', 'edit' ); ?>">
	<label for="old_password">Old Password:</label>
	<input type="password" name="old_password" id="old_password" /><br/>
	<label for="new_password_1">New Password:</label>
	<input type="password" name="new_password_1" id="new_password_1" /><br/>
	<label for="new_password_2">Repeat New Password:</label>
	<input type="password" name="new_password_2" id="new_password_2" /><br/>
	<input type="submit" value="Change Password" name="change_password"/>
</form>
	<?php endif; ?>
</div>
</div>
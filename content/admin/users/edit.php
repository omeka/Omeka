<?php
// Layout: default;
$user = $__c->users()->adminEdit();
$saved = self::$_session->getValue('user_form_saved');
?>
<?php include( 'subnav.php' ); ?>
<br/>

<h3 class="flash"><?php echo self::$_session->flash(); ?></h3>

<h1>Edit User Information</h1>

<form method="post" action="<?php echo $_link->to('users', 'edit'); ?><?php if (@$user->user_id) echo $user->user_id; ?>">

<input type="hidden" name="user[user_id]" value="<?php echo issetor($saved['user']['user_id'], $user->user_id); ?>">

<?php include( 'form.php' ); ?>

<input type="submit" name="user_edit" value="Edit this User -&gt;"></input>

</form>

<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
<br/>
<br/>
<form method="post" action="<?php echo $_link->to('users', 'delete'); ?>" onsubmit="return confirm('Are you sure you want to delete this user?')">
	<input type="hidden" name="user_id" value="<?php echo $user->user_id; ?>"></input>
	<input type="Submit" value="Delete this User -&gt;"></input>
</form>
<?php endif; ?>
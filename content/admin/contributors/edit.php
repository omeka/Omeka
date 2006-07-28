<?php
//Layout: default;
$contributor = $__c->contributors()->edit();
?>

<?php include( 'subnav.php' ); ?>

<br/>

<form method="post" action="<?php echo $_link->to( 'contributors', 'edit' ); ?>" id="contributors-form">

<fieldset>
	<label for="contributor_id">Contributor's internal database id</label>
	<br/>
	<input type="text" name="contributor[contributor_id]" id="contributor_id" value="<?php echo $contributor->getId(); ?>" readonly ></input>
</fieldset>

<?php include( 'form.php' ); ?>

<input type="submit" name="contributor_edit" value="Edit Contributor &gt;&gt;" />

</form>
<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
<br/>
<form method="post" action="<?php echo $_link->to('contributors', 'delete'); ?>">
<input type="hidden" name="contributor_id" value="<?php echo $contributor->getId(); ?>" ></input>
<input type="submit" name="contributor_delete" value="Delete Contributor &gt;&gt;" onclick="return confirm( 'Are you sure you want to delete this user?  All of their associated objects will no longer be assigned a contributor, but not deleted.');">
</form>
<?php endif; ?>
<?php
// Layout: default;
$type = $__c->types()->edit();
$saved = self::$_session->getValue( 'type_form_saved' );
$more_fields = self::$_request->getProperty('type_metafield_number');
?>
<h2>Edit Item Type <?php if($type->type_name) { echo '<span class="typename">'.$type->type_name.'</span>'; } ?></h2>
<form method="post" action="<?php echo $_link->to('types', 'edit').$type->getId(); ?>">

<input type="hidden" name="type[type_id]" value="<?php echo $type->type_id; ?>" />

<?php include( 'form.php' ); ?>

<input type="submit" name="type_edit" value="Edit this Type -&gt;" />

</form>
<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
<form method="post" action="<?php echo $_link->to('types', 'delete'); ?>" onsubmit="return confirm('Are you sure you want to delete this type?')">
	<input type="hidden" name="type_id" value="<?php echo $type->type_id; ?>" />
	<input type="Submit" value="Delete this Type -&gt;" />
</form>
<?php endif; ?>
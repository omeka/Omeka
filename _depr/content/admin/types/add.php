<?php
//Layout: default;
$__c->types()->add();
$saved = self::$_session->getValue( 'type_form_saved' );
?>
<?php include( 'subnav.php' ); ?>
<h2>Add an Item Type</h2>

<form method="post" action="<?php $_link->to('types', 'add'); ?>">

<?php include( 'form.php' ); ?>
<p>Before adding this type, double check that everything is right.  If it is, continue:</p>
<input type="submit" value="Add this Type" name="item_type_add" />

</form>
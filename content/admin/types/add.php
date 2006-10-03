<?php
//Layout: default;
$__c->types()->add();
$saved = self::$_session->getValue( 'type_form_saved' );
?>
<?php include( 'subnav.php' ); ?>
<h2>Add an Item Type</h2>

<form method="post" action="create" onsubmit="if( this.submitted ) return true; else return false;">

<?php include( 'form.php' ); ?>
<p>Before adding this type, double check that everything is right.  If it is, continue:</p>
<input type="hidden" name="type_submitted" value="type_submitted"/>
<input type="button" value="Add this Type" id="item_type_form_submit" onclick="this.form.submitted = true; this.form.submit(); return true;" />

</form>
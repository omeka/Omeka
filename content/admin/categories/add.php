<?php
//Layout: default;
$__c->categories()->add();
$saved = self::$_session->getValue( 'category_form_saved' );
?>
<?php include( 'subnav.php' ); ?>

<form method="post" action="create" onsubmit="if( this.submitted ) return true; else return false;">

<?php include( 'form.php' ); ?>

<p>Before adding this category, double check that everything is right.  If it is, continue:</p>
<input type="hidden" name="category_submitted" value="category_submitted"/>
<input type="button" value="Add this Category" id="object_category_form_submit" onclick="this.form.submitted = true; this.form.submit(); return true;" />

</form>
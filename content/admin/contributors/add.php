<?php
//Layout: default;
$contributor = $__c->contributors()->add();
?>

<?php include( 'subnav.php' ); ?>

<br/>

<form method="post" action="<?php echo $_link->to( 'contributors', 'add' ); ?>" id="contributors-form">
	
<?php include( 'form.php' ); ?>

<input type="submit" name="contributor_add" value="Add Contributor &gt;&gt;" />

</form>
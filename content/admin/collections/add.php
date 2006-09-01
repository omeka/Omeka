<?php
// Layout: default;
$collection = $__c->collections()->add();
?>
<?php include( 'subnav.php' ); ?>
<br/>

<h3 class="flash"><?php echo self::$_session->flash(); ?></h3>

<h1>Create a Collection</h1>

<form method="post" action="add">

<?php include( 'form.php' ); ?>

<input type="submit" name="collection_add" value="Create this Collection -&gt;">

</form>
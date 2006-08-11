<?php
// Layout: default;
$category = $__c->categories()->edit();
$saved = self::$_session->getValue( 'category_form_saved' );
?>
<?php include( 'subnav.php' ); ?>
<br/>

<h3 class="flash"><?php echo self::$_session->flash(); ?></h3>

<h1>Edit the Collection</h1>

<form method="post" action="<?php echo $_link->to('categories', 'edit'); ?>">

<input type="hidden" name="category[category_id]" value="<?php echo $category->category_id; ?>">

<?php include( 'form.php' ); ?>

<input type="submit" name="category_edit" value="Edit this Category -&gt;"></input>

</form>

<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
<br/>
<br/>
<form method="post" action="<?php echo $_link->to('categories', 'delete'); ?>" onsubmit="return confirm('Are you sure you want to delete this category?')">
	<input type="hidden" name="category_id" value="<?php echo $category->category_id; ?>"></input>
	<input type="Submit" value="Delete this Category -&gt;"></input>
</form>
<?php endif; ?>
<?php
// Layout: default;
$category = $__c->categories()->edit();
$saved = self::$_session->getValue( 'category_form_saved' );
$more_fields = self::$_request->getProperty('category_metafield_number');
?>
<h2>Edit Item Type <?php if($category->category_name) { echo '<span class="categoryname">'.$category->category_name.'</span>'; } ?></h2>
<form method="post" action="<?php echo $_link->to('categories', 'edit').$category->getId(); ?>">

<input type="hidden" name="category[category_id]" value="<?php echo $category->category_id; ?>" />

<?php include( 'form.php' ); ?>

<input type="submit" name="category_edit" value="Edit this Category -&gt;" />

</form>
<?php if( self::$_session->getUser()->getPermissions() <= 10 ): ?>
<form method="post" action="<?php echo $_link->to('categories', 'delete'); ?>" onsubmit="return confirm('Are you sure you want to delete this category?')">
	<input type="hidden" name="category_id" value="<?php echo $category->category_id; ?>" />
	<input type="Submit" value="Delete this Category -&gt;" />
</form>
<?php endif; ?>
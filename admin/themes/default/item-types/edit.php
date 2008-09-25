<?php head(array('title'=>'Edit Type: '.htmlentities($itemtype->name),'body_class'=>'types')); ?>
<h1><?php echo htmlentities($itemtype->name); ?></h1>

<div id="primary">
<form method="post" action="">
	<?php include 'form.php';?>
<input type="submit" name="submit" value="Save Changes" class="submit submit-medium" /></p>
	<p id="delete_link"><a class="delete" href="<?php echo record_uri($itemtype, 'delete', 'item-types'); ?>">Delete This Type</a></p>
</form>

<div id="element-form">
<?php 
// Render the add-element action, which renders the element-form partial.
echo $this->action('add-element', 'item-types', null, array('item-type-id'=>$itemtype->id)); ?>
</div>

</div>
<?php foot(); ?>
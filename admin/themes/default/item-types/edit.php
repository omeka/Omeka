<?php head(array('title'=>'Edit Type: '.htmlentities($itemtype->name),'body_class'=>'types')); ?>

<div id="primary">
<h1><?php echo htmlentities($itemtype->name); ?></h1>
<form method="post" action="">
	<?php include 'form.php';?>
	<p id="form_submits"><button type="submit" name="submit">Save Changes</button> or <a class="cancel" href="<?php echo url_for_record($itemtype, 'show', 'item-types'); ?>">Cancel</a></p>
	<p id="delete_link"><a class="delete" href="<?php echo url_for_record($itemtype, 'delete', 'item-types'); ?>">Delete This Type</a></p>
</form>

<?php echo $this->action('add-element', 'item-types'); ?>

</div>
<?php foot(); ?>
<?php head(array('title'=>'Edit Tags', 'body_class'=>'tags')); ?>
<?php common('archive-nav'); ?>
<?php echo flash(); ?>

<div id="primary">
	<h1>Rename Tags</h1>
<form method="post">
<select name="old_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>

<label for="new_tag">New name:</label>
<input type="text" name="new_tag" />

<input type="submit" name="submit" value="Rename this tag" />

</form>
</div>
<?php foot(); ?>
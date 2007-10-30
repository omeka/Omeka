<?php head(array('title'=>'Remove Tags', 'body_class'=>'tags')); ?>
<?php echo flash(); ?>
<?php common('archive-nav'); ?>

<div id="primary">
	<h1>Delete Tags</h1>
<form method="post">
<select name="delete_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>

<input type="submit" name="submit" value="Delete this tag" />

</form>
</div>
<?php foot(); ?>
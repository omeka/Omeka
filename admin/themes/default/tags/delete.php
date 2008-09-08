<?php head(array('title'=>'Remove Tags', 'content_class' => 'horizontal-nav','body_class'=>'tags')); ?>
<?php echo flash(); ?>

<h1>Delete Tags</h1>
<?php common('tags-nav'); ?>
<div id="primary">
<form method="post">
    <label for="delete_tag">Select a tag to delete:</label>
<select name="delete_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>

<input type="submit" name="submit" class="submit-medium" value="Delete this tag" />

</form>
</div>
<?php foot(); ?>
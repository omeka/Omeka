<?php head(array('title'=>'Edit Tags', 'content_class' => 'horizontal-nav','body_class'=>'tags')); ?>

<h1>Rename Tags</h1>
<?php common('tags-nav'); ?>
<div id="primary">
    <?php echo flash(); ?>
    
<form method="post">
<select name="old_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>

<label for="new_tag">New name:</label>
<input type="text" name="new_tag" />

<input type="submit" name="submit" class="submit-medium" value="Rename this tag" />

</form>
</div>
<?php foot(); ?>
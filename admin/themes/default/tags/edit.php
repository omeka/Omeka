<?php head(array('title'=>'Edit Tags', 'content_class' => 'horizontal-nav','body_class'=>'tags')); ?>

<h1>Rename Tags</h1>
<?php common('tags-nav'); ?>
<div id="primary">
   
<?php echo flash(); ?>
    
<form method="post">
	<div class="field">
	<?php echo label('old_tag','Choose a tag to rename');?>
	<div class="inputs">
<select name="old_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>
</div>
</div>
<div class="field">
	<label for="new_tag">Enter a new tag name:</label>
	<div class="inputs">
		<input type="text" size="40" name="new_tag" />
	</div>
</div>
<input type="submit" name="submit" class="submit submit-medium" value="Save Changes" />

</form>
</div>
<?php foot(); ?>
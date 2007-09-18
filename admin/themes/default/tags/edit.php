<?php head(); ?>
<?php echo flash(); ?>
<?php common('archive-nav'); ?>

<div id="primary">
	<h1>Edit Tags</h1>
<form method="post">
<select name="old_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>

<input type="text" name="new_tag" />

<input type="submit" name="submit" value="Edit these Tags" />

</form>
</div>
<?php foot(); ?>
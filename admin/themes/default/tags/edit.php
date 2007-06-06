<?php head(); ?>

<form method="post">
<select name="old_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>

<input type="text" name="new_tag" />

<input type="submit" name="submit" value="Edit these Tags" />

</form>

<?php foot(); ?>
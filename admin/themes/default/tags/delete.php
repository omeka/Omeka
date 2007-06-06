<?php head(); ?>
<?php echo flash(); ?>
<form method="post">
<select name="delete_tag">
	<?php foreach( $tags as $key => $tag ): ?>
		<option value="<?php echo $tag['id']; ?>"><?php echo $tag['name'];?> (<?php echo $tag['tagCount']; ?>)</option>
	<?php endforeach; ?>
</select>

<input type="submit" name="submit" value="Delete this tag" />

</form>

<?php foot(); ?>
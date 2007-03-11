<?php head(); ?>
<h2>Edit Settings</h2>

<form method="post">
	<label>Site Title</label><input type="text" name="site_title" value="<?php echo $site_title; ?>" />
	<input type="text" name="copyright" value="<?php echo $copyright ?>" />
	<input type="text" name="meta_keywords" value="<?php echo $meta_keywords; ?>" />
	<input type="text" name="meta_author" value="<?php echo $meta_author;?>" />
	<input type="text" name="meta_description" value="<?php echo $meta_description;?>" />
	<input type="submit" name="submit" value="Edit the settings" />
</form>

<?php foot(); ?>
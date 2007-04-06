<?php head(); ?>
<?php echo flash(); ?>
<h2>Edit Settings</h2>

<form method="post">
	<label>Site Title</label><input type="text" name="site_title" value="<?php echo $site_title; ?>" />
	<?php 
		text(array('name'=>'copyright', 'id'=>'copyright','value'=>$copyright), 'Copyright');
		text(array('name'=>'meta_keywords', 'id'=>'meta_keywords','value'=>$meta_keywords), 'META Keywords');
		text(array('name'=>'meta_author', 'id'=>'meta_author','value'=>$meta_author), 'META Author');
		text(array('name'=>'meta_description', 'id'=>'meta_description','value'=>$meta_description), 'META Description');
		text(array('name'=>'thumbnail_width', 'id'=>'thumbnail_width','value'=>$thumbnail_width), 'Thumbnail Width');
		text(array('name'=>'thumbnail_height', 'id'=>'thumbnail_height','value'=>$thumbnail_height), 'Thumbnail Height');
		text(array('name'=>'fullsize_width', 'id'=>'fullsize_width','value'=>$fullsize_width), 'Fullsize Width');
		text(array('name'=>'fullsize_height', 'id'=>'fullsize_height','value'=>$fullsize_height), 'Fullsize Height'); 
		text(array('name'=>'path_to_convert', 'id'=>'path_to_convert','value'=>$path_to_convert), 'Local Path to ImageMagick binary (required for thumbnail generation)');
	?>	
	<input type="submit" name="submit" value="Edit the settings" />
</form>

<?php foot(); ?>
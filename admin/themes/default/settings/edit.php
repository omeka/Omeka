<?php head(); ?>
<?php common('settings-nav'); ?>
<?php echo flash(); ?>
<h2>Edit Settings</h2>

<form method="post">
	<fieldset>
<div class="field">
	<?php text(array('name'=>'site_title', 'class'=>'textinput', 'id'=>'site_title'),$site_title, 'Site Title'); ?>
</div>

<div class="field">	
<?php text(array('name'=>'copyright', 'class'=>'textinput', 'id'=>'copyright'),$copyright, 'Copyright'); ?>
</div>

<div class="field">
<?php textarea(array('name'=>'meta_keywords', 'class'=>'textinput', 'id'=>'meta_keywords','rows'=>'5'),$meta_keywords, 'Keywords'); ?>
</div>

<div class="field">    
<?php text(array('name'=>'meta_author', 'class'=>'textinput', 'id'=>'meta_author'),$meta_author, 'Author'); ?>
</div>

<div class="field">    
<?php textarea(array('name'=>'meta_description', 'class'=>'textinput', 'id'=>'meta_description','rows'=>'10'),$meta_description, 'Description'); ?>
</div>

<div class="field">    
<?php text(array('name'=>'thumbnail_width', 'class'=>'textinput', 'id'=>'thumbnail_width'),$thumbnail_width, 'Thumbnail Width'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'thumbnail_height', 'class'=>'textinput', 'id'=>'thumbnail_height'),$thumbnail_height, 'Thumbnail Height'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'fullsize_width', 'class'=>'textinput', 'id'=>'fullsize_width'),$fullsize_width, 'Fullsize Width'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'fullsize_height', 'class'=>'textinput', 'id'=>'fullsize_height'),$fullsize_height, 'Fullsize Height'); ?> 
</div>

<div class="field">
	<?php text(array('name'=>'path_to_convert', 'class'=>'textinput', 'id'=>'path_to_convert'),$path_to_convert, 'Local Path to ImageMagick binary (required for thumbnail generation)'); ?>
</div>
	
	<input type="submit" name="submit" value="Edit the settings" />
	<?php //submit('Edit Settings','submit'); ?>
	</fieldset>
</form>

<?php foot(); ?>
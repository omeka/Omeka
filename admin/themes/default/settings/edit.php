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
<?php text(array('name'=>'author', 'class'=>'textinput', 'id'=>'author'),$author, 'Author'); ?>
</div>

<div class="field">    
<?php textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10'),$description, 'Description'); ?>
</div>

<div class="field">    
<?php text(array('name'=>'thumbnail_constraint', 'class'=>'textinput', 'id'=>'thumbnail_constraint'),$thumbnail_constraint, 'Thumbnail Size Constraint'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'fullsize_constraint', 'class'=>'textinput', 'id'=>'fullsize_constraint'),$fullsize_constraint, 'Fullsize Size Constraint'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'path_to_convert', 'class'=>'textinput', 'id'=>'path_to_convert'),$path_to_convert, 'Local Path to ImageMagick binary (required for thumbnail generation)'); ?>
</div>
	
	<input type="submit" name="submit" value="Edit the settings" />
	<?php //submit('Edit Settings','submit'); ?>
	</fieldset>
</form>

<?php foot(); ?>
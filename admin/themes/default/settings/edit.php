<?php head(); ?>
<?php common('settings-nav'); ?>
<div id="primary">
<?php echo flash(); ?>
<h2>Edit Settings</h2>

<?php fire_plugin_hook('prepend_to_settings_form'); ?>

<form method="post">
	<fieldset>
<div class="field">
	<?php text(array('name'=>'site_title', 'class'=>'textinput', 'id'=>'site_title'),$site_title, 'Site Title'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'administrator_email', 'class'=>'textinput', 'id'=>'administrator_email'),$administrator_email, 'Administrator Email'); ?>
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
	<?php text(array('name'=>'square_thumbnail_constraint', 'class'=>'textinput', 'id'=>'square_thumbnail_constraint'),$square_thumbnail_constraint, 'Square Thumbnail Constraint'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'fullsize_constraint', 'class'=>'textinput', 'id'=>'fullsize_constraint'),$fullsize_constraint, 'Fullsize Size Constraint'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'path_to_convert', 'class'=>'textinput', 'id'=>'path_to_convert'),$path_to_convert, 'Local Path to ImageMagick binary (required for thumbnail generation)'); ?>
</div>

<?php fire_plugin_hook('append_to_settings_form'); ?>	

	</fieldset>
	<input type="submit" name="submit" value="Save Changes" />
	<?php //submit('Edit Settings','submit'); ?>
</form>
</div>
<?php foot(); ?>
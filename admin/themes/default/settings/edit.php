<?php head(array('title'=>'Edit Settings', 'body_class'=>'settings')); ?>
<?php common('settings-nav'); ?>
<?php echo js('tooltip'); ?>

<script type="text/javascript">
//<![CDATA[

Event.observe(window,'load', function() {
	Omeka.Form.makeTooltips($$('.tooltip'));
});
//]]>
</script>
<div id="primary">
<?php echo flash(); ?>
<h2>Edit Settings</h2>

<?php fire_plugin_hook('prepend_to_settings_form'); ?>

<form method="post" action="">
	<fieldset>
<div class="field">
	<label for="site_title">Site Title</label>
	<?php echo text(array('name'=>'site_title', 'class'=>'textinput'),$site_title); ?>
	<span class="tooltip" id="site_title_tooltip">The title of your website</span>
</div>

<div class="field">
	<label for="administrator_email">Administrator Email</label>
	<?php echo text(array('name'=>'administrator_email', 'class'=>'textinput'),$administrator_email); ?>
	<span class="tooltip" id="administrator_email_tooltip">The email address of your site&#8217;s administrator</span>
	
</div>

<div class="field">
	<label for="copyright">Copyright</label>
	<?php echo text(array('name'=>'copyright', 'class'=>'textinput'),$copyright); ?>
	<span class="tooltip" id="copyright_tooltip">Copyright for the site.</span>
	
</div>

<div class="field">
	<label for="author">Author</label>
	<?php echo text(array('name'=>'author', 'class'=>'textinput'),$author); ?>
	<span class="tooltip" id="author_tooltip">The author of the site</span>
	
</div>

<div class="field">    
	<label for="description">Description</label>
	<?php echo textarea(array('name'=>'description', 'class'=>'textinput', 'rows'=>'10'),$description); ?>
	<span class="tooltip" id="description_tooltip">A description for your site</span>
	
</div>

<div class="field">    
	<label for="thumbnail_constraint">Thumbnail Constraint</label>
	<?php echo text(array('name'=>'thumbnail_constraint', 'class'=>'textinput'),$thumbnail_constraint); ?>
	<span class="tooltip" id="thumbnail_constraint_tooltip">The maximum size (in pixels) of the longest side for thumbnails of uploaded images.</span>
	
</div>

<div class="field">
	<label for="square_thumbnail_constraint">Square Thumbnail Constraint</label>
	<?php echo text(array('name'=>'square_thumbnail_constraint', 'class'=>'textinput'),$square_thumbnail_constraint); ?>
	<span class="tooltip" id="square_thumbnail_constraint_tooltip">The maximum size (in pixels) for square thumbnails of uploaded images.</span>
	
</div>

<div class="field">
	<label for="fullsize_constraint">Fullsize Constraint</label>
	<?php echo text(array('name'=>'fullsize_constraint', 'class'=>'textinput'),$fullsize_constraint); ?>
	<span class="tooltip" id="fullsize_constraint_tooltip">The maximum size (in pixels) of the longest side for fullsize versions of uploaded images.</span>
	
</div>

<div class="field">
	<label for="path_to_convert">Path to Convert</label>
	<?php echo text(array('name'=>'path_to_convert', 'class'=>'textinput'),$path_to_convert); ?>
	<span class="tooltip" id="path_to_convert_tooltip">The path to your ImageMagick library.</span>
	
</div>

<?php fire_plugin_hook('append_to_settings_form'); ?>	

	</fieldset>
	
	<fieldset>
	    <input type="submit" name="submit" value="Save Changes" />
	</fieldset>
</form>
</div>
<?php foot(); ?>
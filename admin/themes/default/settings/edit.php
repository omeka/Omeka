<?php head(array('title'=>'Edit Settings', 'body_class'=>'settings')); ?>
<?php common('settings-nav'); ?>
<?php js('tooltip'); ?>

<script type="text/javascript">
function makeTooltips()
{
	//Now load the tooltip js
		var tooltipIds = ['site_title', 'administrator_email', 'copyright', 'author', 'description', 'thumbnail_constraint', 'square_thumbnail_constraint', 'fullsize_constraint', 'path_to_convert'];
		
	//foobar(tooltipIds);
	for (var i=0; i < tooltipIds.length; i++) {
		var elId = tooltipIds[i];
		$(elId).style.cursor = "help";
		var image = document.createElement('img');
		image.src = "<?php echo img('information.png'); ?>";
		$(elId).appendChild(image);
		$(elId).style.paddingLeft = "20px";
		var tooltipId = elId + '_tooltip';
		var tooltip = new Tooltip(image, tooltipId, {default_css:true, zindex:100000});
		$(tooltipId).addClassName('info-window');
	};
}

Event.observe(window,'load', function() {
	makeTooltips();
});
</script>
<div id="primary">
<?php echo flash(); ?>
<h2>Edit Settings</h2>

<?php fire_plugin_hook('prepend_to_settings_form'); ?>

<form method="post">
	<fieldset>
<div class="field">
	<label for="site_title" id="site_title">Site Title</label>
	<?php text(array('name'=>'site_title', 'class'=>'textinput'),$site_title); ?>
	<span class="tooltip" id="site_title_tooltip">The title of your website</span>
	
</div>

<div class="field">
	<label for="administrator_email" id="administrator_email">Administrator Email</label>
	<?php text(array('name'=>'administrator_email', 'class'=>'textinput'),$administrator_email); ?>
	<span class="tooltip" id="administrator_email_tooltip">The email address of your site&#8217;s administrator</span>
	
</div>

<div class="field">
	<label for="copyright" id="copyright">Copyright</label>
	<?php text(array('name'=>'copyright', 'class'=>'textinput'),$copyright); ?>
	<span class="tooltip" id="copyright_tooltip">Copyright for the site.</span>
	
</div>

<div class="field">
	<label for="author" id="author">Author</label>
	<?php text(array('name'=>'author', 'class'=>'textinput'),$author); ?>
	<span class="tooltip" id="author_tooltip">The author of the site</span>
	
</div>

<div class="field">    
	<label for="description" id="description">Description</label>
	<?php textarea(array('name'=>'description', 'class'=>'textinput', 'rows'=>'10'),$description); ?>
	<span class="tooltip" id="description_tooltip">A description for your site</span>
	
</div>

<div class="field">    
	<label for="thumbnail_constraint" id="thumbnail_constraint">Thumbnail Constraint</label>
	<?php text(array('name'=>'thumbnail_constraint', 'class'=>'textinput'),$thumbnail_constraint); ?>
	<span class="tooltip" id="thumbnail_constraint_tooltip">The maximum size (in pixels) of the longest side for thumbnails of uploaded images.</span>
	
</div>

<div class="field">
	<label for="square_thumbnail_constraint" id="square_thumbnail_constraint">Square Thumbnail Constraint</label>
	<?php text(array('name'=>'square_thumbnail_constraint', 'class'=>'textinput'),$square_thumbnail_constraint); ?>
	<span class="tooltip" id="square_thumbnail_constraint_tooltip">The maximum size (in pixels) for square thumbnails of uploaded images.</span>
	
</div>

<div class="field">
	<label for="fullsize_constraint" id="fullsize_constraint">Fullsize Constraint</label>
	<?php text(array('name'=>'fullsize_constraint', 'class'=>'textinput'),$fullsize_constraint); ?>
	<span class="tooltip" id="fullsize_constraint_tooltip">The maximum size (in pixels) of the longest side for fullsize versions of uploaded images.</span>
	
</div>

<div class="field">
	<label for="path_to_convert" id="path_to_convert">Square Thumbnail Constraint</label>
	<?php text(array('name'=>'path_to_convert', 'class'=>'textinput'),$path_to_convert); ?>
	<span class="tooltip" id="path_to_convert_tooltip">The path to your ImageMagick library.</span>
	
</div>

<?php fire_plugin_hook('append_to_settings_form'); ?>	

	</fieldset>
	<input type="submit" name="submit" value="Save Changes" />
	<?php //submit('Edit Settings','submit'); ?>
</form>
</div>
<?php foot(); ?>
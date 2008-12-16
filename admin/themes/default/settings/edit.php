<?php head(array('title'=>'Edit Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('tooltip'); ?>

<script type="text/javascript">
//<![CDATA[

Event.observe(window,'load', function() {
	Omeka.Form.makeTooltips($$('.tooltip'));
});
//]]>
</script>
<h1>General Settings</h1>

<?php common('settings-nav'); ?>

<div id="primary">
<?php echo flash(); ?>

<form method="post" action="" id="settings-form">
	<fieldset>
	<?php $siteSettings = array(
	    array('name'=>'site_title', 'description' => 'The title of your website.'),
	    array('name'=>'administrator_email', 'description' => 'The email address of your site&#8217;s administrator.'),
	    array('name'=>'copyright', 'description' => 'Copyright for the site.'),
	    array('name'=>'author', 'description' => 'The author of the site.'),
	    array('name'=>'description', 'description' => 'A description for your site.'),
	    array('name'=>'thumbnail_constraint', 'description' => 'The maximum size (in pixels) of the longest side for thumbnails of uploaded images.'),
	    array('name'=>'square_thumbnail_constraint', 'description' => 'The maximum size (in pixels) for square thumbnails of uploaded images.'),
	    array('name'=>'fullsize_constraint', 'description' => 'The maximum size (in pixels) of the longest side for fullsize versions of uploaded images.'),
	    array('name'=>'per_page_admin', 'description' => 'Limit the number of items displayed per page in the administrative interface.'),
	    array('name'=>'per_page_public', 'description' => 'Limit the number of items displayed per page in the public interface.'),
	    array('name'=>'path_to_convert', 'description' => 'The path to your ImageMagick library.')); ?>    

<?php foreach ($siteSettings as $key => $setting): ?>
    <div class="field">
        <?php $settingName =  $setting['name']; ?>
        <label for="<?php echo $settingName; ?>"><?php echo ucwords(Inflector::humanize($settingName)); ?></label>
<div class="inputs">
        <?php echo $this->formText($settingName, $$settingName, array('class'=>'textinput', 'size'=>'30')); ?>
        <p class="explanation"><?php echo $setting['description']; ?></p>
</div>
    </div>
<?php endforeach; ?>
	</fieldset>
	
	<fieldset>
	    <input type="submit" id="settings-submit" name="submit" class="submit submit-medium" value="Save Changes" />
	</fieldset>
</form>
</div>
<?php foot(); ?>
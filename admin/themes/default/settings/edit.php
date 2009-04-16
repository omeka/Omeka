<?php head(array('title'=>'Edit Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
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
	    array('name'=>'path_to_convert', 'description' => 'The path to your ImageMagick library.  Include only the directory path, not the executable file.  For example, if it is located at &quot;/usr/bin/convert&quot;, put only &quot;/usr/bin&quot;.')); ?>    

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
	    <legend>Security Settings</legend>
	    <div class="field">
	        <label for="file_extension_whitelist">Allowed File Extensions</label>
	        <div class="inputs">
	        <?php echo $this->formTextarea('file_extension_whitelist', 
	                get_option('file_extension_whitelist'), 
	                array('class'=>'textinput', 'cols'=>50, 'rows'=>5)); ?>
	        <p class="explanation">List of extensions that are allowed for files 
	            in the Omeka archive.  This list may be adjusted as necessary, 
	            but keep in mind that many types of files can represent a 
	            security risk.</p> 
	        </div>
	    </div>
	    
	    <div class="field">
	        <label for="file_mime_type_whitelist">Allowed File Types</label>
	        <div class="inputs">
	        <?php echo $this->formTextarea('file_mime_type_whitelist',
	                get_option('file_mime_type_whitelist'),
	                array('class'=>'textinput', 'cols'=>50, 'rows'=>5)); ?>
	        <p class="explanation">List of types of files that are allowed in the
	            Omeka archive.  This list may be adjusted as necessary, but keep
	            in mind that many types of files can represent a security risk.</p>
	        </div>
	    </div>
	    
	    <div class="field">
	        <label for="disable_default_file_validation">Disable the Default
	            Validation for File Uploads</label>
	        <?php echo $this->formCheckbox('disable_default_file_validation', 
	            null, array('checked'=>get_option('disable_default_file_validation'))); ?>
	        <p class="explanation warning">WARNING: This will allow any file to
	            be uploaded to Omeka.  This is not recommended for production 
	            sites or sites where file upload responsibilities are not tightly
	            supervised.</p>
	    </div>
	</fieldset>
	
	<fieldset>
	    <input type="submit" id="settings-submit" name="submit" class="submit submit-medium" value="Save Changes" />
	</fieldset>
</form>
</div>
<?php foot(); ?>
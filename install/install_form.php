<?php
// FIXME: Cannot use html_escape() because MVC (including view helpers) is not
// loaded by Installer.
function formValue($name, $defaultValue = '')
{
    return isset($_POST[$name]) ? htmlentities($_POST[$name]): htmlentities($defaultValue);
}
?>
<form method="post" accept-charset="utf-8" id="install-form">
    <fieldset>
        <legend>Site Settings</legend>
        <div class="field">
            <label for="site_title">Site Name</label>
            <input type="text" name="site_title" class="textinput" id="site_title" value="<?php echo formValue('site_title'); ?>" />
        </div>
        <div class="field">
            <label for="admin_email">Administrator Email (required)</label>
            <input type="text" name="administrator_email" class="textinput" id="admin_email" value="<?php echo formValue('administrator_email'); ?>" />
        </div>
        <div class="field">
            <label for="copyright">Copyright Info</label>
            <input type="text" name="copyright" class="textinput" id="copyright" value="<?php echo formValue('copyright'); ?>" />
        </div>
        <div class="field">
            <label for="author">Author Info</label>
            <input type="text" class="textinput" name="author" id="author" value="<?php echo formValue('author'); ?>" />
        </div>
        <div class="field">
            <label for="description">Site Description</label>
            <textarea name="description" class="textinput" id="description"><?php echo formValue('description'); ?></textarea>
        </div>
        <div class="field">
            <label for="thumbnail_constraint">Maximum Thumbnail Size Constraint (px)</label>
            <input type="text" class="textinput" name="thumbnail_constraint" id="thumbnail_constraint" value="<?php echo formValue('thumbnail_constraint', Installer::DEFAULT_THUMBNAIL_CONSTRAINT); ?>" />
        </div>
        <div class="field">
            <label for="square_thumbnail_constraint">Maximum Square Thumbnail Size Constraint (px)</label>
            <input type="text" class="textinput" name="square_thumbnail_constraint" id="square_thumbnail_constraint" value="<?php echo formValue('square_thumbnail_constraint', Installer::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT); ?>" />
        </div>
        <div class="field">
            <label for="fullsize_constraint">Maximum Fullsize Image Size Constraint (px)</label> 
            <input type="text" class="textinput" name="fullsize_constraint" id="fullsize_constraint" value="<?php echo formValue('fullsize_constraint', Installer::DEFAULT_FULLSIZE_CONSTRAINT); ?>" />
        </div>
        <div class="field">
            <label for="per_page_admin">Limit the number of items displayed per page in the administrative interface.</label>
            <input type="text" name="per_page_admin" class="textinput" id="per_page_admin" value="<?php echo formValue('per_page_admin', '10'); ?>" />
        </div>
        <div class="field">
            <label for="per_page_public">Limit the number of items displayed per page in the public interface.</label>
            <input type="text" name="per_page_public" class="textinput" id="per_page_public" value="<?php echo formValue('per_page_public', '10'); ?>" />
        </div>
        <div class="field">
            <label for="path_to_convert">Imagemagick Directory Path</label>
            <input type="text" name="path_to_convert" class="textinput" id="path_to_convert" value="<?php echo formValue('path_to_convert', $installer->getPathToConvert()); ?>" />
        </div>
    </fieldset>
    <fieldset>
        <legend>Default Super User Account</legend>
        <div class="field">
            <label for="username">Username (required)</label>
            <input type="text" class="textinput" name="username" value="<?php echo formValue('username'); ?>" />
        </div>
        <div class="field">
            <label for="password">Password (required)</label>
            <input class="textinput" type="password" name="password" value="<?php echo formValue('password'); ?>"/>
<p class="explanation">Must be a least six characters</p>
        </div>
        <div class="field">
            <label for="super_email">Email (required)</label>
            <input class="textinput" type="text" name="super_email" id="super_email" value="<?php echo formValue('super_email'); ?>">
        </div>
    </fieldset>
    <p><input type="submit" class="submit submit-medium" value="Continue" name="install_submit" /></p>
</form>

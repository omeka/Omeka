<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Installation</title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="install.css" />
</head>

<body>
	<div id="wrap">
<?php
require_once 'install.php';

if ($display_form == true):
?>
<div id="intro">
<h1>Welcome to Omeka!</h1>

<?php if(isset($error)): ?>
	<div class="error">
	The following errors occurred:<br />
	<?php echo nl2br(htmlentities($error)); ?>
	</div>
<?php endif; ?>
<p>To complete the installation process, please fill out the form below:</p>
</div>
<form method="post" accept-charset="utf-8" id="install-form">
	<fieldset>
	<legend>Site Settings</legend>
	<div class="field">
	<label for="site_title">Site Name</label>
	<input type="text" name="site_title" class="textinput" id="site_title" value="<?php echo htmlentities($_POST['site_title']); ?>" />
	</div>
	<div class="field">
	<label for="admin_email">Administrator Email (required for form emails)</label>
	<input type="text" name="administrator_email" class="textinput" id="admin_email" value="<?php echo htmlentities($_POST['administrator_email']); ?>" />
	</div>
	<div class="field">
	<label for="copyright">Copyright Info</label>
	<input type="text" name="copyright" class="textinput" id="copyright" value="<?php echo htmlentities($_POST['copyright']); ?>" />
	</div>
	<div class="field">
	<label for="author">Author Info</label>
	<input type="text" class="textinput" name="author" id="author" value="<?php echo $_POST['author']; ?>" />
	</div>
	<div class="field">
	<label for="description">Site Description</label>
	<textarea name="description" class="textinput" id="description"><?php echo htmlentities($_POST['description']); ?></textarea>
	</div>
	<div class="field">
	<label for="thumbnail_constraint">Maximum Thumbnail Size Constraint (px)</label>
	<input type="text" class="textinput" name="thumbnail_constraint" id="thumbnail_constraint" value="<?php echo (!empty($_POST['thumbnail_constraint']) ? htmlentities($_POST['thumbnail_constraint']) : 150); ?>" />
	</div>
	<div class="field">
	<label for="square_thumbnail_constraint">Maximum Square Thumbnail Size Constraint (px)</label>
	<input type="text" class="textinput" name="square_thumbnail_constraint" id="square_thumbnail_constraint" value="<?php echo (!empty($_POST['square_thumbnail_constraint']) ? htmlentities($_POST['square_thumbnail_constraint']) : 100); ?>" />
	</div>
	<div class="field">
	<label for="fullsize_constraint">Maximum Fullsize Image Size Constraint (px)</label> 
	<input type="text" class="textinput" name="fullsize_constraint" id="fullsize_constraint" value="<?php echo (!empty($_POST['fullsize_constraint']) ? htmlentities($_POST['fullsize_constraint']) : 600); ?>" />
	</div>
	<div class="field">
	<label for="path_to_convert">Imagemagick Binary Path</label>
	<input type="text" name="path_to_convert" class="textinput" id="path_to_convert" value="
	<?php if ($path_to_convert) { 
			echo "$path_to_convert";
		  } else {
			echo htmlentities($_POST['path_to_convert']);
		} ?>" />
	</div>
	</fieldset>
	<fieldset>
	<legend>Default Super User Account</legend>
	<div class="field">
	<label for="username">Username</label>
	<input type="text" class="textinput" name="username" value="<?php echo htmlentities($_POST['username']); ?>" />
	</div>
	<div class="field">
	<label for="password">Password</label>
	<input class="textinput" type="password" name="password" value="<?php echo htmlentities($_POST['password']); ?>"/>
	</div>
	<div class="field">
		<label for="super_email">Email</label>
		<input class="textinput" type="text" name="super_email" id="super_email" value="<?php echo htmlentities($_POST['super_email']); ?>">
	</div>
	
	</fieldset>
	<p><input type="submit" value="Continue" name="install_submit" /></p>
</form>
<?php endif; ?>
</div>
</body>
</html>
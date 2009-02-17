<?php 
// Define the web and directory paths.
require_once '../paths.php'; ?>
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
	<div id="primary">
    
<?php
try {
    require_once 'Installer.php';
    $installer = new Installer();
    if ($installer->getShowInstallForm()) {
        ?>
            <h1>Installation Complete!</h1>
            <?php if ($warningMessage = $installer->getWarningMessage()): ?>
            <div class="error">
            <?php echo $warningMessage; ?>
            </div>
            <?php endif; ?>
            <?php if ($installer->getInstallFormValidationErrorExists()): ?>
            <div class="error">
            <?php echo $installer->getInstallFormValidationErrorMessage(); ?>
            </div>
            <?php endif; ?>
            <p>To complete the installation process, please fill out the form below:</p>
        <?php
        include 'install_form.php';
    } else {
        ?>
        <div id="intro">
            <h1>Installation is Finished!</h1>
            <p>Omeka is now installed. <a href="<?php echo dirname($_SERVER['REQUEST_URI']); ?>">Check out your site</a>, 
                or go directly to the <a href="<?php echo dirname($_SERVER['REQUEST_URI']) . '/admin'; ?>">admin</a> panel.</p>
        </div>
        <?php
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</div>

</div>
</body>
</html>

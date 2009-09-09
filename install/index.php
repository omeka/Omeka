<?php
require_once '../paths.php';

// This mini app is in the install/ directory.
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/** Zend_Application */
require_once 'Zend/Application.php';  

require_once 'Installer.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/application.ini'
);


$application->getBootstrap()->bootstrap('FrontController')->run();

exit;
$installer = new Installer();
$installer->checkRequirements();
?>

<?php if ($installer->hasError()): /* Display installation errors. */?>
    <h2>Installation Error</h2>
    <p>Before installation can continue, the following errors must be resolved:</p>
    <ol>
    <?php foreach($installer->getErrorMessages() as $errorMessage): ?>
        <li>
            <h3 class="error"><?php echo $errorMessage['header']; ?></h3>
            <p><?php echo $errorMessage['message']; ?></p>
        </li>
    <?php endforeach; ?>
    </ol>
<?php else: /* Display and validate form, install Omeka. */?>
    <?php $form = $installer->getForm(); ?>
    <?php if(isset($_POST['install_submit']) && $form->isValid($_POST)): /* Install the database if the form validates. */?>
        <?php if ($installer->installDatabase()): ?>
            <h2>Installation Successful!</h2>
            <p>Omeka is now installed. <a href="../">Check out your site</a>, or visit your <a href="../admin/">admin panel</a>.</p>
        <?php else: ?>
            <h2>There was a problem. Omeka did not install properly.</h2>
        <?php endif; ?>
    <?php else: /* Display the form, including installation warnings and validation errors. */?>
        <?php if ($installer->hasWarning()): /* Display warnings. */?>
            <h2>Installation Warning</h2>
            <p>The following issues will not affect installation, but they may 
            negatively affect the behavior of Omeka:</p>
            <ol>
            <?php foreach($installer->getWarningMessages() as $warningMessage): ?>
                <li>
                    <h3 class="warning"><?php echo $warningMessage['header']; ?></h3>
                    <p><?php echo $warningMessage['message']; ?></p>
                </li>
            <?php endforeach; ?>
            </ol>
        <?php endif; ?>
        <h2>Configure Your Site <span id="required-note">* Required Fields</span></h2>
        <?php if (isset($_POST['install_submit'])): /* Assume the form did not validate. */?>
            <h3 class="validation-error">Form Validation Errors</h3>
            <p>There were errors found in your form. Please edit and resubmit.</p>
        <?php endif; ?>
        <?php echo $form; /* Display the install form. */?>
    <?php endif; ?>
<?php endif; ?>

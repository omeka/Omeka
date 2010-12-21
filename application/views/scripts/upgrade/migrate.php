<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>Upgrade Omeka</title>
    <link rel="stylesheet" media="all" href="<?php echo html_escape(css('style')); ?>">
</head>
<body id="upgrade">
    <div id="content">
<?php if(!$success): ?>
    <h1>Omeka encountered an error when upgrading your installation:</h1>
	<p class="error_text"><?php echo html_escape($error); ?></p>
    <?php if ($debugMode): ?>
        <pre id="debug"><?php echo html_escape($trace); ?></pre>
    <?php endif; ?>
    <p class="instruction">Please restore from your database backup and try again.
        If you continue to experience errors, you can notify us on the Omeka 
        <a href="http://omeka.org/forums/">Forums</a>.</p>
<?php else: ?>
    <h1>Omeka has upgraded successfully.</h1>
    <p><?php echo link_to_admin_home_page('Return to Dashboard'); ?></p>    
<?php endif; ?>	
</div>
</body>
</html>
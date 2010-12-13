<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>Upgrade Omeka</title>
    <link rel="stylesheet" media="all" href="<?php echo html_escape(css('style')); ?>">
</head>
<body id="upgrade">
    <div id="content">
        <h1>Upgrade Your Omeka Database</h1> 
        <p>Your Omeka database is not compatible with your current
        version of Omeka. Please backup your existing database and then click the button to upgrade:</p>
        <?php echo link_to('upgrade', 'migrate', 'Upgrade Database', array('id' => 'upgrade-database-link', 'class'=>'button')); ?>                        
    </div>
</body>
</html>
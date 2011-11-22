<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?php echo __('Upgrade Omeka'); ?></title>
    <link rel="stylesheet" media="all" href="<?php echo html_escape(css('style')); ?>">
</head>
<body id="upgrade">
    <div id="content">
        <h1><?php echo __('Upgrade Your Omeka Database'); ?></h1> 
        <p>
            <?php echo __('Your Omeka database is not compatible with your current version of Omeka.'); ?>
            <?php echo __('Please back up your existing database and then click the button to upgrade.'); ?>'
        </p>
        <?php echo link_to('upgrade', 'migrate', __('Upgrade Database'), array('id' => 'upgrade-database-link', 'class'=>'button')); ?>                        
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?php echo __('Upgrade Omeka'); ?></title>
    <link rel="stylesheet" media="all" href="<?php echo html_escape(css('style')); ?>">
</head>
<body id="upgrade">
    <div id="content">
    <h1><?php echo __('Omeka Upgrade Completed'); ?></h1>
    <p><?php echo __('Your Omeka database is completely up-to-date.'); ?> <?php echo __('Please return to the %1$s or your site&#8217;s %2$s', link_to_admin_home_page('admin'), link_to_home_page('home page')); ?>. 
    <?php echo
    __('If you have any questions please refer to <a href="http://omeka.org/codex">Omeka documentation</a> or post a message on the <a href="http://omeka.org/forums">Omeka forums</a>.'); ?>
    </p>
    </div>
</body>
</html>

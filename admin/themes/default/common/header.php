<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Admin: <?php echo settings('site_title'); ?> | <?php echo $title; ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('screen')); ?>" />
<link rel="stylesheet" media="print" href="<?php echo html_escape(css('print')); ?>" />

<!--[if IE 7]>
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('ie7')); ?>" />
<![endif]-->

<!--[if lte IE 6]>
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('lte-ie6')); ?>" />
<![endif]-->

<!-- JavaScripts -->
<?php echo js('default'); ?>
<?php echo js('globals'); ?>
<?php echo js('archive'); ?>
<?php echo js('livepipe'); ?>
<?php echo js('tabs'); ?>

<!-- Plugin Stuff -->
<?php admin_plugin_header(); ?>

</head>
<body class="<?php echo $bodyclass; ?>">
    <?php if (OMEKA_MIGRATION > (int) get_option('migration')): ?>
        <div id="upgrade-database">
            <h2>Upgrade Your Omeka Database</h2> 
            <p>Your Omeka database is not compatible with your current
            version of Omeka.  

            <?php if (has_permission('Upgrade', 'migrate')): ?>
                Please backup your existing database and then upgrade:</p>
                <?php echo link_to('upgrade', 'migrate', 'Upgrade', array('id' => 'upgrade-database-link', 'class'=>'button')); ?>                    
                
            <?php else: ?>
                Please notify an administrator to upgrade the database.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="hide"><a href="#content">Skip Navigation/Skip to Content</a></div>
    <div id="wrap">
        
        <div id="header">
            <div id="site-title"><?php echo link_to_admin_home_page(settings('site_title')); ?></div>
            
            <div id="site-info">
                <p id="welcome">Welcome, <a href="<?php echo html_escape(uri('users/edit/'.current_user()->id)); ?>"><?php echo html_escape(current_user()->first_name); ?></a> | <a href="<?php echo html_escape(uri('users/logout'));?>" id="logout">Logout</a></p>
                <?php if (has_permission('Settings', 'edit')): ?>
                    <a href="<?php echo html_escape(uri('settings')); ?>" id="settings-link">Settings</a>';

                <?php endif; ?>
                <?php echo link_to_home_page('View Public Site', array('id'=>'public-link')); ?>
                <?php echo plugin_append_to_admin_site_info(); ?>
            </div>
            

                                    
            <?php echo common('primary-nav'); ?>
        </div>
        
        
        
        <div id="content" <?php if(!empty($content_class)) echo ' class="'. $content_class .'"'; ?>>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Admin: <?php echo settings('site_title'); ?> | <?php echo $title; ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<?php $this->headLink()->appendStylesheet(css('reset'), 'screen', false)
                       ->appendStylesheet(css('components'), 'screen', false)
                       ->appendStylesheet(css('screen'), 'screen', false)
                       ->appendStylesheet(css('print'), 'print', false)
                       ->appendStylesheet(css('ie7'), 'screen', 'IE 7')
                       ->appendStylesheet(css('lte-ie6'), 'screen', 'lte IE 6'); 
?>

<!-- JavaScripts -->
<?php $this->headScript()->appendFile(web_path_to("javascripts/prototype.js"))
                         ->appendFile(web_path_to("javascripts/prototype-extensions.js"))
                         ->appendFile(web_path_to("javascripts/scriptaculous.js") . '?load=effects,dragdrop,controls')
                         ->appendFile(web_path_to("javascripts/globals.js"))
                         ->appendFile(web_path_to("javascripts/livepipe.js"))
                         ->appendFile(web_path_to("javascripts/tabs.js"))
                         ->appendFile(web_path_to("javascripts/search.js"));
?>

<!-- Plugin Stuff -->
<?php admin_plugin_header(); ?>
<?php echo $this->headLink(); ?>
<?php echo $this->headScript(); ?>
</head>
<body class="<?php echo $bodyclass; ?>">
    <div class="hide"><a href="#content">Skip Navigation/Skip to Content</a></div>
    <div id="wrap">
        
        <div id="header">
            <div id="site-title"><?php echo link_to_admin_home_page(settings('site_title')); ?></div>
            
            <div id="site-info">
            <?php if (current_user()): ?>
                <p id="welcome">Welcome, <?php if (has_permission(current_user(), 'edit')): ?>
                    <a href="<?php echo html_escape(uri('users/edit/'.current_user()->id)); ?>"><?php echo html_escape(current_user()->first_name); ?></a>
                <?php else: ?>
                    <?php echo html_escape(current_user()->first_name); ?>
                <?php endif; ?> | <a href="<?php echo html_escape(uri('users/logout'));?>" id="logout">Logout</a></p>
            <?php endif; ?>
                <?php if (has_permission('Settings', 'edit')): ?>
                    <a href="<?php echo html_escape(uri('settings')); ?>" id="settings-link">Settings</a>';

                <?php endif; ?>
                <?php echo link_to_home_page('View Public Site', array('id'=>'public-link')); ?>
                <?php echo plugin_append_to_admin_site_info(); ?>
            </div>
            

                                    
            <?php echo common('primary-nav'); ?>
        </div>
        
        
        
        <div id="content" <?php if(!empty($content_class)) echo ' class="'. $content_class .'"'; ?>>

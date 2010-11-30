<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Admin: <?php echo settings('site_title'); echo isset($title) ? ' | ' . $title : ''; ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
    queue_css('default', 'all');
    queue_css('jquery-ui', 'screen');

    queue_js('globals');
?>

<!-- Plugin Stuff -->
<?php admin_plugin_header(); ?>

<!-- Stylesheets -->
<?php display_css(); ?>

<!-- JavaScripts -->
<?php display_js(); ?>

</head>
<body<?php echo isset($bodyclass) ? ' class="'.$bodyclass.'"' : ''; ?>>
    <div class="hide"><a href="#content">Skip Navigation/Skip to Content</a></div>
    <div id="wrap">
        
        <div id="header">
            <div id="site-title"><?php echo link_to_admin_home_page(settings('site_title')); ?></div>
            
            <div id="site-info">
                <?php if (current_user()): ?>
                    <p id="welcome">Welcome, <a href="<?php echo html_escape(uri('users/edit/'.current_user()->id)); ?>"><?php echo html_escape(current_user()->first_name); ?></a> | <a href="<?php echo html_escape(uri('users/logout'));?>" id="logout">Logout</a></p>
                <?php endif; ?>
                <?php if (has_permission('Settings', 'edit')): ?>
                    <a href="<?php echo html_escape(uri('settings')); ?>" id="settings-link">Settings</a>';

                <?php endif; ?>
                <?php echo link_to_home_page('View Public Site', array('id'=>'public-link')); ?>
                <?php echo plugin_append_to_admin_site_info(); ?>
            </div>
            

                                    
            <?php echo common('primary-nav'); ?>
        </div>
        
        
        
        <div id="content"<?php echo isset($content_class) ? ' class="'.$content_class.'"' : ''; ?>>

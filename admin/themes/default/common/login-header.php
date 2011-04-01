<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?php echo settings('site_title'); ?></title>
    
    <!-- Stylesheets -->
    <?php queue_css('default'); ?>
    <?php display_css(); ?>

    <!-- JavaScripts -->
    <?php display_js(); ?>

    <!-- Plugin Stuff -->
    <?php admin_plugin_header(); ?>
</head>
<body id="login">
    <div id="wrap">
        <div id="header">
            <div id="site-title"><?php echo link_to_admin_home_page(); ?></div>
        </div>
        <div id="content">

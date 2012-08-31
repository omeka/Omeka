<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta charset="utf-8">
    <title><?php echo settings('site_title'); ?></title>
    
    <!-- Stylesheets -->
    <?php queue_css('style'); ?>
    <?php queue_css('layout'); ?>
    <?php queue_css('skeleton'); ?>
    <?php display_css(); ?>
    <link href='http://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

    <!-- JavaScripts -->
    <?php display_js(); ?>

    <!-- Plugin Stuff -->
    <?php fire_plugin_hook('admin_theme_header'); ?>
</head>
<body id="login">

    <div class="container container-sixteen">
    
        <div id="content" class="login-content eight columns offset-by-four">
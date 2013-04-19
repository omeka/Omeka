<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta charset="utf-8">
    <title><?php echo option('site_title'); ?></title>
    
    <!-- Stylesheets -->
    <?php queue_css_file('style'); ?>
    <?php queue_css_file('layout'); ?>
    <?php queue_css_file('skeleton'); ?>
    <?php echo head_css(); ?>
    <link href='//fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

    <!-- JavaScripts -->
    <?php
    queue_js_file('vendor/modernizr');
    queue_js_file('vendor/selectivizr', 'javascripts', array('conditional' => '(gte IE 6)&(lte IE 8)'));
    queue_js_file('vendor/respond');

    echo head_js();
    ?>

    <!-- Plugin Stuff -->
    <?php fire_plugin_hook('admin_head', array('view'=>$this)); ?>
</head>
<body id="login">
    <div class="container container-sixteen">
        <div id="content" class="login-content ten columns offset-by-three">

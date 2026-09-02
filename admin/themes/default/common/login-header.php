<!DOCTYPE html>
<html lang="<?php echo get_html_lang();?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta charset="utf-8">
    <title><?php echo option('site_title'); ?></title>
    
    <!-- Stylesheets -->
    <?php
    queue_css_file(['core-fonts', 'style', 'layout', 'skeleton']);
    echo head_css();
    ?>

    <!-- JavaScripts -->
    <?php
    queue_js_file('vendor/modernizr');
    queue_js_file('vendor/selectivizr', 'javascripts', ['conditional' => '(gte IE 6)&(lte IE 8)']);
    queue_js_file('vendor/respond');

    echo head_js();
    ?>

    <!-- Plugin Stuff -->
    <?php fire_plugin_hook('admin_head', ['view'=>$this]); ?>
</head>
<body id="login">
    <div class="container container-sixteen">
        <div id="content" class="login-content ten columns offset-by-three">

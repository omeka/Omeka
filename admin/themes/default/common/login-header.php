<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="all" href="<?php echo html_escape(css('default')); ?>" />

<!-- JavaScripts -->
<?php queue_js('login'); ?>
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

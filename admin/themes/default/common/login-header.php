<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('screen')); ?>" />
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('login')); ?>" />

<!--[if lte IE 6]>
<link rel="stylesheet" media="screen" href="<?php echo html_escape(css('lte-ie6')); ?>" />
<![endif]-->

<!-- JavaScripts -->
<?php echo js('prototype'); ?>

<!-- Plugin Stuff -->
<?php admin_plugin_header(); ?>

</head>
<body id="login">
    <div id="wrap">
        <div id="header">
            <div id="site-title"><?php echo link_to_admin_home_page(); ?></div>
        </div>
        <div id="content">
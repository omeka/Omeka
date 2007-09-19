<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php css('screen'); ?>" />
<link rel="stylesheet" media="print" href="<?php css('print'); ?>" />

<!-- JavaScripts -->
<?php js('prototype'); ?>
<?php js('scriptaculous');?>
<?php js('niftycube');?>
<?php js('globals'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body id="login">
	<div id="wrap">
		<div id="header">
			<div id="site-title"><a href="<?php echo uri(''); ?>"><?php settings('site_title'); ?></a></div>
		</div>
		<div id="content">
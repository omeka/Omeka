<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php exhibit_css('screen'); ?>" />
<link rel="stylesheet" media="screen" href="<?php layout_css('layout'); ?>">
<link rel="stylesheet" media="print" href="<?php css('print'); ?>" />

<!-- JavaScripts -->
<?php js('prototype'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body id="<?php echo $exhibit->theme; ?>">

<div id="wrap">
	<h5><a href="<?php echo uri('exhibits'); ?>">Back to Exhibits</a></h5>
	<h1><?php link_to_exhibit($exhibit); ?></h1>

<div id="exhibit-nav">
	<?php section_nav();?>
</div>

<div id="content">
	<?php page_nav(); ?>
<?php echo flash(); ?>				

		
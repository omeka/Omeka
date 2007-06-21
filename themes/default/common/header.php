<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta type="description" content="<?php echo settings('description'); ?>" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php css('screen'); ?>" />
<link rel="stylesheet" media="print" href="<?php css('print'); ?>" />

<!-- JavaScripts -->
<?php js('prototype'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body>
	<div id="wrap">
		<div id="header">
			<div class="padding">
			<h1><a href="<?php echo uri(''); ?>"><?php settings('site_title'); ?></a></h1>
			<ul id="primary-nav" class="navigation">
			<?php
				nav(array('Items' => uri('items/browse'), 'Collections' => uri('collections'),'Exhibits' => uri('exhibits')));

			?>
			</ul>
			</div>
		</div>
		<div id="content">
			<div class="padding">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php echo settings('description'); ?>" />

<?php echo auto_discovery_link_tag(); ?>

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php css('screen'); ?>" />
<link rel="stylesheet" media="print" href="<?php css('print'); ?>" />
<link rel="stylesheet" media="screen" href="<?php layout_css(); ?>" />

<!-- JavaScripts -->
<?php js('prototype'); ?>
<?php js('cb'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body>
	<div id="wrap">
		
		<div id="header" class="cbb">
			<h1><a href="<?php echo uri(''); ?>"><?php settings('site_title'); ?></a></h1>
			<h3><?php settings('description'); ?></h3>
		</div><!--end header-->

		<div id="nav" class="cbb">
			<ul id="primary-nav" class="navigation">
			<?php nav(array('Items' => uri('items/browse'), 'Exhibits' => uri('exhibits'), 'Collections' => uri('collections'))); ?><li><a href="<?php echo uri('about'); ?>">About</a></li>
			</ul>
		</div>

		<div id="content" class="clear">
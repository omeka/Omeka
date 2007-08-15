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
<link rel="stylesheet" media="screen" href="<?php layout_css(); ?>" />
<!-- JavaScripts -->
<?php js('prototype'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body>
	<div id="wrap">
		<?php common('search'); ?>
		<div id="header">
			<h1><a href="<?php echo uri(''); ?>"><?php settings('site_title'); ?></a></h1>
			<h5><?php settings('description'); ?></h5>
			<ul id="primary-nav" class="navigation">
			<?php
				nav(array('Inspiration' => uri('items/browse'), 'Exhibits' => uri('exhibits'), 'Collections' => uri('collections')));
			?>
			<li id="news"><a href="#">News</a></li>
			</ul>
		</div>
		<div id="content">

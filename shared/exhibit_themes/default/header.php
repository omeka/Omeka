<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php exhibit_css('screen'); ?>" />
<link rel="stylesheet" media="print" href="<?php css('print'); ?>" />

<!-- JavaScripts -->
<?php js('prototype'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body>
	<div id="wrap">
		<div id="header">
			<h1><a href="<?php echo uri(''); ?>"><?php settings('site_title'); ?></a> - Sample Exhibit Theme (named &#039;default&#039;)</h1>
		</div>
		<div id="content">
	
	<?php echo flash(); ?>				



	<ul>
	<?php foreach( $exhibit->Sections as $key => $sec ): ?>
		<li><?php echo $sec->title;?></li>
	<?php endforeach; ?>
	</ul>
	
	<ul>
	<li>The exhibit's title is: <?php echo $exhibit->title; ?></li>
	<li>The current sections's title is: <?php echo $section->title; ?></li>
	</ul>
	

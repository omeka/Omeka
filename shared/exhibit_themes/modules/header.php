<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php echo settings('description'); ?>" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php css('screen'); ?>" />
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

<div id="header">
	
	<h1 id="logo"><a href="<?php echo uri(''); ?>"></a></h1>

	<div id="searchwrap">
		<form id="searchform" action="search.php" class="clear">
			<input type="text" name="search" id="quick-search"></input>
			<input type="submit" name="submit" value="Search" class="submit"></input>
		</form>
	</div><!--end searchwrap-->

	<ul id="primary-nav" class="navigation">
	<?php
	nav(array('Introductory Essay' => uri('exhibits/intro/1989revolutions'), 'Primary Sources' => uri('primarysources/'),'Analyzing Evidence' => uri('analyzing'),'Teaching Modules'=>uri('teaching'), 'Case Studies'=>uri('exhibits/casestudies/soviet-health-posters')));
	?>
	</ul>

</div><!-- end header -->
		
<div id="content">

		
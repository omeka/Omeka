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
<?php js('globals'); ?>
<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body>
	<div class="hide"><a href="#content">Skip Navigation/Skip to Content</a></div>
	<div id="wrap">
		
		<div id="header">
			<div id="user-meta">Welcome, <?php echo current_user()->first_name; ?>! <a href="<?php echo uri('users/logout');?>" id="logout">Logout</a></div>
			
			<h1><a href="<?php echo uri(''); ?>"><?php settings('site_title'); ?></a></h1>
			<ul id="primary-nav" class="navigation">
			<?php
				nav(array('Home' => uri(''),'Archive' => uri('items/browse'),'Exhibits' => uri('exhibits'),'Users' => uri('users'), 'Settings' =>uri('settings')));

			?>
			</ul>
		</div>
		
		
		
		<div id="content">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Admin: <?php echo settings('site_title'); ?> | <?php echo $title; ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php echo css('screen'); ?>" />
<link rel="stylesheet" media="print" href="<?php echo css('print'); ?>" />

<!--[if lte IE 6]>
<link rel="stylesheet" media="screen" href="<?php echo css('lte-ie6'); ?>" />
<![endif]-->

<!-- JavaScripts -->
<?php echo js('default'); ?>
<?php echo js('globals'); ?>
<?php echo js('archive'); ?>
<?php echo js('livepipe'); ?>
<?php echo js('tabs'); ?>

<!-- Plugin Stuff -->
<?php admin_plugin_header(); ?>

</head>
<body class="<?php echo $body_class; ?>">
	<div class="hide"><a href="#content">Skip Navigation/Skip to Content</a></div>
	<div id="wrap">
		
		<div id="header">
			<div id="site-title"><a href="<?php echo uri(''); ?>"><?php echo settings('site_title'); ?></a></div>
			
			<div id="user-meta">
			    <p id="welcome">Welcome, <a href="<?php echo uri('users/show/'.current_user()->id); ?>"><?php echo current_user()->first_name; ?></a>! <a href="<?php echo uri('users/logout');?>" id="logout">Logout</a></p>
			    <p id="view-public-site"><?php echo link_to_home_page('View Public Site', array('id'=>'public-link')); ?></p>

			    </div>
			

									
            <?php echo common('primary-nav'); ?>
		</div>
		
		
		
		<div id="content" <?php if(!empty($content_class)) echo ' class="'. $content_class .'"'; ?>>

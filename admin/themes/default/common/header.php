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
<link rel="stylesheet" media="screen" href="<?php echo css('niftyCorners'); ?>" />

<link rel="stylesheet" media="screen" href="<?php echo layout_css(); ?>" />
<!--[if lte IE 6]>
<link rel="stylesheet" media="screen" href="<?php echo css('lte-ie6'); ?>" />
<![endif]-->

<!-- JavaScripts -->
<?php echo js('default'); ?>
<?php echo js('niftycube');?>
<?php echo js('form-toggle');?>
<?php echo js('globals'); ?>
<?php echo js('exhibits'); ?>
<?php echo js('archive'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body class="<?php echo $body_class; ?>">
	<div class="hide"><a href="#content">Skip Navigation/Skip to Content</a></div>
	<div id="wrap">
		
		<div id="header">
			<div id="site-title"><a href="<?php echo uri(''); ?>"><?php echo settings('site_title'); ?></a></div>
			<div id="user-meta"><p>Welcome, <?php echo current_user()->first_name; ?>! <a href="<?php echo uri('users/logout');?>" id="logout">Logout</a></p>
			<p><?php echo link_to_home_page('View Public Site', array('id'=>'public-link')); ?></p></div>
									
			<ul id="primary-nav" class="navigation">
			<?php
				$header_navigation = array('Dashboard' => uri(''), 'Archive' => uri('items'));
				
				if(has_permission('exhibits','add')){
				$header_navigation['Exhibits'] = uri('exhibits');
			}
				if(has_permission('Users','browse') ) {
					$header_navigation['Users'] = uri('users/browse');
				}
				
				if(has_permission('entities','add')) {
				
				$header_navigation['Names'] = uri('entities');
				}
				if(has_permission('super')) {
					$header_navigation['Settings'] = uri('settings');
				}
				echo admin_nav($header_navigation);
				fire_plugin_hook('load_navigation', 'main');
			?>
			</ul>
		</div>
		
		
		
		<div id="content">
			
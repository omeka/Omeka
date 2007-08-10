<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Admin: <?php settings('site_title'); ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="<?php css('screen'); ?>" />
<link rel="stylesheet" media="print" href="<?php css('print'); ?>" />
<link rel="stylesheet" media="screen" href="<?php css('niftyCorners'); ?>" />

<!-- JavaScripts -->
<?php js('prototype'); ?>
<?php js('prototype-extensions'); ?>
<?php js('scriptaculous');?>
<?php js('niftycube');?>
<?php js('form-toggle');?>
<?php js('globals'); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

</head>
<body class="<?php echo controller_name(); ?>">
	<div class="hide"><a href="#content">Skip Navigation/Skip to Content</a></div>
	<div id="wrap">
		
		<div id="header">
			<div id="site-title"><a href="<?php echo uri(''); ?>"><?php settings('site_title'); ?></a></div>
			<div id="user-meta">Welcome, <?php echo current_user()->first_name; ?>! <a href="<?php echo uri('users/logout');?>" id="logout">Logout</a><br /><?php link_to_home_page('View Public Site', array('id'=>'public-link')); ?></div>
									
			<ul id="primary-nav" class="navigation">
			<?php
				$header_navigation = array('Dashboard' => uri(''), 'Archive' => uri('items'),'Exhibits' => uri('exhibits') );
				if(has_permission('Users','browse') ) {
					$header_navigation['Users'] = uri('users');
				}
				if(has_permission('super')) {
					$header_navigation['Settings'] = uri('settings');
				}
				$header_navigation['Names'] = uri('entities');
				admin_nav($header_navigation);

			?>
			</ul>
		</div>
		
		
		
		<div id="content">
			
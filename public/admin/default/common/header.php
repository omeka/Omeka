<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title; ?></title>

<link rel="stylesheet" media="screen" href="<?php css('screen'); ?>" />
<link rel="stylesheet" media="print" href="<?php css('print'); ?>" />

<?php js('prototype'); ?>

<?php plugin_header(); ?>

</head>
<body>
	<div id="wrap">


		<div id="header">
			<h1 id="logo"><a href="<?php echo uri(''); ?>">Web Design History <?php echo $title; ?></a></h1>
			<ul id="primary-nav" class="navigation">
				<li id="nav-home"><a href="<?php echo uri(''); ?>">Home</a></li>
				<li id="nav-items"><a href="<?php echo uri('items'); ?>">Items</a></li>
				<li id="nav-types"><a href="<?php echo uri('types'); ?>">Types</a></li>
				<li id="nav-collections"><a href="<?php echo uri('collections'); ?>">Collections</a></li>
				<li id="nav-tags"><a href="<?php echo uri('tags'); ?>">Tags</a></li>
				<li id="nav-themes"><a href="<?php echo uri('themes'); ?>">Themes</a></li>
				<li id="nav-plugins"><a href="<?php echo uri('plugins'); ?>">Plugins</a></li>
				<li id="nav-users"><a href="<?php echo uri('users'); ?>">Users</a></li>
			</ul>
		</div>
		<ul>
		<?php

			nav(array('Items' => uri('items/browse'), 'Themes' => uri('themes/browse')));

		?>
		</ul>
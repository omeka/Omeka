<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo settings('site_title'); echo isset($title) ? ' | ' . $title : ''; ?></title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php echo settings('description'); ?>" />

<?php echo auto_discovery_link_tag(); ?>

<!-- Plugin Stuff -->
<?php plugin_header(); ?>

<!-- Stylesheets -->
<?php
queue_css('style');
display_css(); 
?>

<!-- JavaScripts -->
<?php echo display_js(); ?>

</head>
<body<?php echo isset($bodyid) ? ' id="'.$bodyid.'"' : ''; ?><?php echo isset($bodyclass) ? ' class="'.$bodyclass.'"' : ''; ?>>
<div id="layout">
	<div id="wrap">

	<div id="header">
	<div class="map-light">
		<div id="site-title"><?php echo link_to_home_page(custom_display_logo()); ?></div>
            <div class="hr-line"></div>
            <div class="light-l"></div>
            <div class="light-r"></div>
	</div>
	</div>


    <!--Banner-->
    <div class="globe">
        <div class="map">
<?php echo get_theme_option('subheader_content'); ?>
            <div class="light-l"></div>
            <div class="light-r"></div>
        </div>
    </div>
    <!--banner-->

		
		<div id="content" class="product">
		    <div class=" container">
			<div id="primary-nav">
				<div id="search-wrap">
				    <h2>Search</h2>
				    <?php echo simple_search(); ?>
				    <?php echo link_to_advanced_search(); ?>
    			</div>
    			
    			<ul class="navigation">
    			    <?php echo custom_public_nav_header(); ?>
    			</ul>
			</div>
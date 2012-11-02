<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <?php if ( $description = option('description')): ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php endif; ?>

    <title><?php echo option('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

    <?php echo auto_discovery_link_tags(); ?>

    <!-- Plugin Stuff -->
    <?php fire_plugin_hook('public_theme_header'); ?>

    <!-- Stylesheets -->
    <?php
    queue_css_file('style');
    echo head_css();
    ?>

    <!-- JavaScripts -->
    <?php echo head_js(); ?>
</head>

<?php echo body_tag(array('id' => @$bodyid, 'class' => @$bodyclass)); ?>
    <?php fire_plugin_hook('public_theme_body'); ?>
    <div id="wrap">

        <header role="banner">

            <?php fire_plugin_hook('public_theme_page_header'); ?>

            <?php echo theme_header_image(); ?>

            <div id="search-container">
                <?php echo search_form(); ?>
            </div><!-- end search -->

            <div id="site-title"><?php echo link_to_home_page(theme_logo()); ?></div>
            
            <nav id="top-nav">
                <?php echo public_nav_main(); ?>
            </nav>

        </header>
        
        <article id="content">
        
            <?php fire_plugin_hook('public_theme_page_content'); ?>

<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <?php if ( $description = settings('description')): ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php endif; ?>

    <title><?php echo settings('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

    <?php echo auto_discovery_link_tags(); ?>

    <!-- Stylesheets -->
    <?php
    queue_css('style', 'all');
    queue_css('skeleton', 'all');
    queue_css('layout', 'all');
    display_css();
    ?>
    <link href='http://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>


    <!-- JavaScripts -->
    <?php display_js(); ?>
</head>

<body id="upgrade">

    <div class="container container-sixteen">
    
        <section id="content" class="eight columns offset-by-four">
            <h1><?php echo __('Omeka Upgrade Completed'); ?></h1>
            <p><?php echo __('Your Omeka database is completely up-to-date.'); ?> <?php echo __('Please return to the %1$s or your site&#8217;s %2$s', link_to_admin_home_page('admin'), link_to_home_page('home page')); ?>. 
            <?php echo
            __('If you have any questions please refer to <a href="http://omeka.org/codex">Omeka documentation</a> or post a message on the <a href="http://omeka.org/forums">Omeka forums</a>.'); ?>
            </p>
        </section>
    
    </div>
    
</body>

</html>
<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <?php if ( $description = option('description')): ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php endif; ?>

    <title><?php echo option('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

    <?php echo auto_discovery_link_tags(); ?>

    <!-- Stylesheets -->
    <?php
    queue_css_file(array('style', 'skeleton', 'layout'));
    echo head_css();
    ?>
    <link href='http://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>


    <!-- JavaScripts -->
    <?php echo head_js(); ?>
</head>

<body id="upgrade">

    <div class="container container-sixteen">
    
        <section id="content" class="eight columns offset-by-four">
        
        <h1><?php echo __('Upgrade Your Omeka Database'); ?></h1> 
        <p>
            <?php echo __('Your Omeka database is not compatible with your current version of Omeka.'); ?>
            <?php echo __('Please back up your existing database and then click the button to upgrade.'); ?>
        </p>
        <?php echo link_to('upgrade', 'migrate', __('Upgrade Database'), array('id' => 'upgrade-database-link', 'class'=>'big green button')); ?>
        
        </section>
    
    </div>

</body>

</html>
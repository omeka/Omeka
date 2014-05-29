<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">

    <title><?php echo __('Omeka Admin'); ?>: <?php echo option('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

<?php
    queue_css_file(array('style', 'skeleton', 'jquery-ui'));
    queue_css_file('media/960min', 'only screen and (min-width: 960px)');
    queue_css_file('media/768min', 'only screen and (min-width: 768px) and (max-width: 959px)');
    queue_css_file('media/767max', 'only screen and (max-width: 767px)');
    queue_css_file('media/479max', 'only screen and (max-width: 479px)');
    queue_css_url('//fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic');

    queue_js_file(array('vendor/respond', 'vendor/modernizr', 'vendor/selectivizr', 'globals'));
?>

<!-- Stylesheets -->
<?php echo head_css(); ?>

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

<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">

    <title><?php echo __('Omeka Admin'); ?>: <?php echo option('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

<!-- Stylesheets -->
<?php
queue_css_file(['core-fonts', 'style', 'layout', 'skeleton']);
echo head_css();
?>

<!-- JavaScripts -->
<?php
queue_js_file(['vendor/respond', 'vendor/modernizr', 'vendor/selectivizr', 'globals']);
echo head_js();
?>
</head>

<body id="upgrade">

    <div class="container container-sixteen">
    
        <section id="content" class="eight columns offset-by-four">
        
        <h1><?php echo __('Upgrade Your Omeka Database'); ?></h1> 
        <p>
            <?php echo __('Your Omeka database is not compatible with your current version of Omeka.'); ?>
            <?php echo __('Please back up your existing database and then click the button to upgrade.'); ?>
        </p>
        <?php echo link_to('upgrade', 'migrate', __('Upgrade Database'), ['id' => 'upgrade-database-link', 'class'=>'big green button']); ?>
        
        </section>
    
    </div>

</body>

</html>

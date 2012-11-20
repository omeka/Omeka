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
    queue_css_url('https://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic');

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
            <?php if(!$success): ?>
                <h1><?php echo __('Omeka encountered an error when upgrading your installation.'); ?></h1>
                <p class="error_text"><?php echo html_escape($error); ?></p>
                <?php if ($debugMode): ?>
                    <pre id="debug"><?php echo html_escape($trace); ?></pre>
                <?php endif; ?>
                <p class="instruction"><?php echo __('Please restore from your database backup and try again.'); ?>
                    <?php echo
                    __('If you have any questions please refer to <a href="http://omeka.org/codex">Omeka documentation</a> or post a message on the <a href="http://omeka.org/forums">Omeka forums</a>.'); ?>
                    </p>
                <?php else: ?>
                    <h1><?php echo __('Omeka has upgraded successfully.'); ?></h1>
                    <p><?php echo link_to_admin_home_page(__('Return to Dashboard')); ?></p>    
            <?php endif; ?>
        </section>
    </div>

</body>
</html>

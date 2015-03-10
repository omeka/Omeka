<?php
if ($success):
    $title = __('Omeka has upgraded successfully.');
else:
    $title = __('Omeka encountered an error when upgrading your installation.');
endif;
?>
<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo html_escape($title); ?></title>
    <link rel="stylesheet" media="all" href="<?php echo WEB_VIEW_SCRIPTS . '/css/style.css'; ?>">
    <link rel="stylesheet" media="all" href="<?php echo WEB_VIEW_SCRIPTS . '/css/skeleton.css'; ?>">
    <link rel="stylesheet" media="all" href="<?php echo WEB_VIEW_SCRIPTS . '/css/layout.css'; ?>">
    <link href='//fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
</head>

<body id="upgrade">
    <div class="container container-sixteen">
        <section id="content" class="ten columns offset-by-three">
            <h1><?php echo html_escape($title); ?></h1>
<?php if($success): ?>
            <p><?php echo link_to_admin_home_page(__('Return to Dashboard')); ?></p>
<?php else: ?>
            <p class="error_text"><?php echo html_escape($error); ?></p>
            <pre id="backtrace"><?php echo utf8_htmlspecialchars($exception); ?></pre>
            <p class="instruction">
                <?php echo __('Please restore from your database backup and try again.'); ?>
                <?php echo __('If you have any questions please refer to <a href="http://omeka.org/codex">Omeka documentation</a> or post a message on the <a href="http://omeka.org/forums">Omeka forums</a>.'); ?>
            </p>
<?php endif; ?>
        </section>
    </div>
</body>
</html>

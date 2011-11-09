<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?php echo __('Omeka Has Encountered an Error'); ?></title>
    <link rel="stylesheet" media="all" href="<?php echo html_escape(css('style')); ?>">
</head>
<body id="debug">
    <div id="content">
        <h1><?php echo __('Omeka Has Encountered an Error'); ?></h1>
        <dl id="error-message">
            <dt><?php echo get_class($e); ?></dt>   
            <dd>
                <?php echo nls2p($e->getMessage()) ."\n"; ?>
            </dd>
        </dl>

        <h2><?php echo __('Backtrace'); ?></h2>
        <pre id="backtrace"><?php echo $e; ?></pre>
    </div>
</body>
</html>

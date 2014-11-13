<?php
    $title = (isset($title) && $displayError)
           ? $title
           : __('Omeka has encountered an error');
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" media="all" href="<?php echo WEB_VIEW_SCRIPTS . '/css/style.css'; ?>">
    <link rel="stylesheet" media="all" href="<?php echo WEB_VIEW_SCRIPTS . '/css/skeleton.css'; ?>">
    <link rel="stylesheet" media="all" href="<?php echo WEB_VIEW_SCRIPTS . '/css/layout.css'; ?>">
    <link href='//fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
</head>
<body id="debug">
    <div class="container container-sixteen">
        <div id="content" class="ten columns offset-by-three">
            <h1><?php echo $title; ?></h1>
            <?php if ($displayError): ?>
                <?php if (is_string($e)): ?>
                <p><?php echo nl2br($e); ?></p>
                <?php else: ?>
                <dl id="error-message">
                    <dt><?php echo get_class($e); ?></dt>
                    <dd>
                        <p><?php echo nl2br(htmlspecialchars($e->getMessage())); ?></p>
                    </dd>
                </dl>
                <pre id="backtrace"><?php echo htmlspecialchars($e); ?></pre>
                <?php endif; ?>
            <?php else: ?>
                <p><?php echo __('To learn how to see more detailed information about this error, see the Omeka Codex page on <a href="http://omeka.org/codex/Retrieving_error_messages">retrieving error messages</a>.'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

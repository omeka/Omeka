<?php
$pageTitle = __('404 Page Not Found');
head(array('title'=>$pageTitle)); ?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <p><?php echo __('&#8220;%s&#8221; is not a valid URL.',html_escape($badUri)); ?></p>
</div>
<?php foot(); ?>
<?php head(array('title'=>'Theme Configuration', 'bodyclass'=>'plugins')); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Please Configure The &quot;<?php echo html_escape($theme->title); ?>&quot; Theme</h2>
        <?php echo $configForm; ?>
</div>

<?php foot(); ?>
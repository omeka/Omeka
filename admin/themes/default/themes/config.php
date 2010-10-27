<?php head(array('title'=>'Theme Configuration', 'bodyclass'=>'themes theme-configuration')); ?>
<?php echo js('tiny_mce/tiny_mce'); ?>
<?php echo js('themes'); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Please Configure The &quot;<?php echo html_escape($theme->title); ?>&quot; Theme</h2>
    <p>Configurations apply to this theme only.</p>
        <?php echo $configForm; ?>
</div>

<?php foot(); ?>

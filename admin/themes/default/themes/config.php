<?php
$pageTitle = __('Configure the &#8220;%s&#8221; Theme', html_escape($theme->title));
echo head(array('title'=>$pageTitle, 'bodyclass'=>'themes theme-configuration')); ?>
<?php echo js_src('tiny_mce/tiny_mce'); ?>
<?php echo js_src('themes'); ?>
               <?php echo flash(); ?>

            <p><?php echo __('Configurations apply to this theme only.'); ?></p>
            <?php echo $configForm; ?>

<?php echo foot(); ?>

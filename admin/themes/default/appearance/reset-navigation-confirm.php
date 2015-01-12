<?php
$pageTitle = __('Reset Navigation');

if (!$isPartial):
    echo head(array('title' => $pageTitle));
endif;
?>
<div title="<?php echo $pageTitle; ?>">
    <h2><?php echo __('Are you sure?'); ?></h2>
    <p><?php echo __("All customizations will be lost. Order and labels will revert to Omeka defaults. Custom links will be lost."); ?></p>
    <?php echo $form; ?>
</div>
<?php
if (!$isPartial):
    echo foot();
endif;
?>
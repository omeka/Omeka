<?php
$title = __('System Information');
echo head(array('title' => $title, 'bodyclass' => 'system-info')); ?>

<?php echo flash(); ?>
<table>
<?php foreach ($info as $category => $entries): ?>
    <tr><th colspan="2"><?php echo html_escape($category); ?></th></tr>
    <?php foreach ($entries as $name => $value): ?>
    <tr>
        <td><?php echo html_escape($name); ?></td>
        <td><?php echo html_escape($value); ?></td>
    </tr>
    <?php endforeach; ?>
<?php endforeach; ?>
</table>
<?php fire_plugin_hook('admin_system_info', array('system_info' => $info, 'view' => $this)); ?>
<?php echo foot();

<div id="<?php echo $options['id']; ?>">
<ul>
    <li><?php echo __('Query:');?> <?php echo html_escape($query); ?></li>
    <li><?php echo __('Query type:');?> <?php echo html_escape($query_type); ?></li>
    <li><?php echo __('Record types:');?>
        <ul>
            <?php foreach ($record_types as $record_type): ?>
            <li><?php echo html_escape($record_type); ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
</ul>
</div>

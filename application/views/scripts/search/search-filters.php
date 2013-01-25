<div id="<?php echo $options['id']; ?>">
<ul>
    <li><?php echo __('Query:');?> <?php echo $query; ?></li>
    <li><?php echo __('Query type:');?> <?php echo $query_type; ?></li>
    <li><?php echo __('Record types:');?>
        <ul>
            <?php foreach ($record_types as $record_type): ?>
            <li><?php echo $record_type; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
</ul>
</div>

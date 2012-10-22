<div id="<?php echo $options['id']; ?>">
<ul>
    <li>Query: <?php echo $query; ?></li>
    <li>Query type: <?php echo $query_type; ?></li>
    <li>Record types:
        <ul>
            <?php foreach ($record_types as $record_type): ?>
            <li><?php echo $record_type; ?></li>
            <?php endforeach; ?>
        </ul>
    </li>
</ul>
</div>

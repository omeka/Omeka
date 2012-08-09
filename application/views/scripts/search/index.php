<?php
$pageTitle = __('Search Omeka');
head(array('title' => $pageTitle));
?>
<div id="primary">
    <table>
        <thead>
            <tr>
                <th>Record</th>
                <th>Title</th>
                <th>Relevance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->results as $result): ?>
            <tr>
                <td><?php echo Inflector::titleize($result['record_name']); ?></td>
                <td><a href="<?php echo $this->url($result['record']->getRecordRoute($result['record']->id)); ?>"><?php echo $result['title'] ? $result['title'] : '[Unknown]'; ?></a></td>
                <td><?php echo $result['relevance']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php foot(); ?>
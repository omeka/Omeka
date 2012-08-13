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
                <td><?php echo Inflector::titleize($result['record_type']); ?></td>
                <td><a href="<?php echo record_uri($result['record'], 'show'); ?>"><?php echo $result['title'] ? $result['title'] : '[Unknown]'; ?></a></td>
                <td><?php echo $result['relevance']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php foot(); ?>
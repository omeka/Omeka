<?php
$pageTitle = __('Search Omeka');
head(array('title' => $pageTitle));
?>
<div id="primary">
    <table>
        <thead>
            <tr>
                <th>Record Type</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->results as $result): ?>
            <tr>
                <td><?php echo Inflector::titleize($result['record_type']); ?></td>
                <td><a href="<?php echo record_url($result['record'], 'show'); ?>"><?php echo $result['title'] ? $result['title'] : '[Unknown]'; ?></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php foot(); ?>
<?php
$pageTitle = __('Search Omeka');
echo head(array('title' => $pageTitle));
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
            <?php foreach ($this->searchTexts as $searchText): ?>
            <tr>
                <td><?php echo Inflector::titleize($searchText['record_type']); ?></td>
                <td><a href="<?php echo record_url($searchText['record'], 'show'); ?>"><?php echo $searchText['title'] ? $searchText['title'] : '[Unknown]'; ?></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php echo foot(); ?>
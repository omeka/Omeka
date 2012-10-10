<?php
$pageTitle = __('Search Omeka');
echo head(array('title' => $pageTitle));
$searchRecordTypes = get_search_record_types();
?>
<div id="primary">
<div id="pagination-top" class="pagination"><?php echo pagination_links(); ?></div>
<table>
    <thead>
        <tr>
            <th>Record Type</th>
            <th>Title</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (loop('search_texts') as $searchText): ?>
        <?php $record = get_record_by_id($searchText['record_type'], $searchText['record_id']); ?>
        <tr>
            <td><?php echo $searchRecordTypes[$searchText['record_type']]; ?></td>
            <td><a href="<?php echo record_url($record, 'show'); ?>"><?php echo $searchText['title'] ? $searchText['title'] : '[Unknown]'; ?></a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php echo foot(); ?>
<?php
$pageTitle = __('Search Omeka ' . __('(%s total)', $total_results));
echo head(array('title' => $pageTitle));
$searchRecordTypes = get_search_record_types();
?>
<div id="primary">
<?php echo search_filters(); ?>
<?php if ($total_results): ?>
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
<div id="pagination-bottom" class="pagination"><?php echo pagination_links(); ?></div>
<?php else: ?>
<p>Your query returned no results.</p>
<?php endif; ?>
</div>
<?php echo foot(); ?>
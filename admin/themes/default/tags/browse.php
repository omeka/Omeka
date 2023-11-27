<?php
$canEdit = is_allowed('Tags', 'edit');
$canDelete = is_allowed('Tags', 'delete');

$pageTitle = __('Browse Tags');
$pageTitle .= ' ' . __('(%s total)', $total_results);

if ($canEdit) {
    queue_js_file(array('tags', 'vendor/jquery-editable-poshytip.min'));
}

echo head(array('title'=>$pageTitle,'bodyclass'=>'tags browse-tags'));
echo flash();

?>

<?php if ($canEdit): ?>
<section class="three columns alpha">
    <h2><?php echo __('Editing Tags'); ?></h2>

    <ol>
        <li><?php echo __('This number counts all records associated with a tag. Filtering "Record types" to "Items" will provide links to all items containing the tag.'); ?></li>
        <li><?php echo __('To edit the tag name, click the name and begin editing, and hit "enter" to save. To cancel an edit, click the ESC key or click away from the tag.'); ?></li>
        <li><?php echo __('To delete a tag, click the X. Deleting a tag will not delete the tagged records.'); ?></li>
    </ol>
</section>
<?php endif; ?>

<?php $tagsSectionClass = ($canEdit) ? 'seven' : 'ten alpha'; ?>

<form id="search-tags" method="GET" class="<?php echo $tagsSectionClass; ?> columns omega">
    <input type="text" name="like" aria-labelledby="search-tags-button"/>
    <button class="green button" type="submit" id="search-tags-button"><?php echo __('Search tags'); ?></button>
    <?php if(isset($params['type'])): ?>
    <input type="hidden" name="type" value="<?php echo $params['type']; ?>"/>
    <?php endif; ?>

    <select class="quick-filter" aria-label="<?php echo __('Record Types'); ?>">
        <option><?php echo __('Record Types'); ?></option>
        <option value="<?php echo $this->url(); ?>"><?php echo __('All'); ?></option>
        <?php foreach($record_types as $record_type): ?>
        <option value="<?php echo url('tags', array('type' => $record_type)); ?>"><?php echo __($record_type); ?></option>
        <?php endforeach; ?>
        </select>
</form>

<div id="search-filters" class="<?php echo $tagsSectionClass; ?> columns omega">
    <ul>
        <li><?php echo __('Record Type') . ': ' . __($browse_for); ?></li>
        <?php if (!empty($params['like'])): ?><li><?php echo __('Name') .' '. __('contains') . ': "' . html_escape($params['like']) .'"'; ?></li><?php endif; ?>
    </ul>
    <?php if (!empty($params['like']) || !empty($params['type'])): ?><a href="<?php echo $this->url() ?>" class="blue small button"><?php echo __('Reset results') ?></a><?php endif; ?>
</div>

<section class="<?php echo $tagsSectionClass; ?> columns omega">

<?php if ($total_results): ?>

    <?php
        $paginationLinks = pagination_links();
        echo $paginationLinks;
    ?>

    <section>
        <div id="tags-nav">
            <?php
            $sortOptions = array(
                __('Name') => array('sort_field' => 'name', 'sort_dir'=> ($sort['sort_field'] == 'name' && $sort['sort_dir'] == 'a') ? 'd' : 'a'),
                __('Count') => array('sort_field' => 'count', 'sort_dir' => ($sort['sort_field'] == 'count' && $sort['sort_dir'] == 'd') ? 'a' : 'd'),
                __('Date created') => array('sort_field' => 'time', 'sort_dir' => ($sort['sort_field'] == 'time' && $sort['sort_dir'] == 'a') ? 'd' : 'a')
            );

            foreach ($sortOptions as $label => $sortParams) {
                $uri = html_escape(current_url($sortParams + $params));
                $class = '';
                if ($sort['sort_field'] == $sortParams['sort_field']) {
                    $sortDirClass = $sort['sort_dir'] == 'd' ? 'desc' : 'asc';
                    $class = ' class="current '. $sortDirClass .'"';
                }

                echo "<span $class><a class='button' href=\"$uri\">$label</a></span>";
            }
            ?>
        </div>
        <ul class="tag-list">
        <?php foreach ($tags as $tag): ?>
            <li>
            <?php if($browse_for == 'Item'):?>
                <a href="<?php echo html_escape(url('items/browse', array('tags' => $tag->name))); ?>" class="count"><?php echo $tag['tagCount']; ?></a>
            <?php else: ?>
                <span class="count"><?php echo $tag['tagCount']; ?></span>
            <?php endif; ?>
            <?php if ($canEdit): ?>
                <span class="tag edit-tag" data-pk="<?php echo $tag->id; ?>"><?php echo html_escape($tag->name); ?></span>
            <?php else: ?>
                <span class="tag"><?php echo html_escape($tag->name); ?></span>
            <?php endif; ?>
            <?php if ($canDelete): ?>
                <span class="delete-tag"><?php echo link_to($tag, 'delete-confirm', 'delete', array('class' => 'delete-confirm')); ?></span>
            <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php fire_plugin_hook('admin_tags_browse', array('tags' => $tags, 'view' => $this)); ?>
    <?php echo $paginationLinks; ?>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>
</section>

<script type="text/javascript">
jQuery(document).ready(function () {
    Omeka.addReadyCallback(Omeka.quickFilter);
    <?php if($canEdit): ?>
    var editableURL = '<?php echo url('tags/rename-ajax'); ?>';
    var tagURLBase = '<?php echo url('items/browse?tags='); ?>';
    var csrfToken = <?php echo js_escape($csrfToken); ?>;
    Omeka.Tags.enableEditInPlace(editableURL, tagURLBase, csrfToken);
    <?php endif; ?>
});
</script>

<?php echo foot(); ?>

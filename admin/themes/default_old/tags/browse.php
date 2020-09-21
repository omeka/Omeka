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

<form id="search-tags" method="GET" class="three columns alpha">
    <input type="text" name="like" aria-label="<?php echo __('Search tags'); ?>"/>    <button><?php echo __('Search tags'); ?></button>
    <?php if(isset($params['type'])): ?>
    <input type="hidden" name="type" value="<?php echo $params['type']; ?>"/>
    <?php endif; ?>
</form>

<div id="search-filters" class="seven columns omega">
    <ul>
        <li><?php echo __('Record Type') . ': ' . __($browse_for); ?></li>
        <?php if (!empty($params['like'])): ?><li><?php echo __('Name') .' '. __('contains') . ': "' . html_escape($params['like']) .'"'; ?></li><?php endif; ?>
    </ul>
    <?php if (!empty($params['like']) || !empty($params['type'])): ?><a href="<?php echo $this->url() ?>" class="blue button"><?php echo __('Reset results') ?></a><?php endif; ?>
</div>


<?php if ($total_results): ?>
    <div class="clearfix">
    <?php
        $paginationLinks = pagination_links();
        echo $paginationLinks;
    ?>
    </div>
    <?php if ($canEdit): ?>
    <section class="three columns alpha">
        <h2><?php echo __('Editing Tags'); ?></h2>
        
        <ol>
            <li><?php echo __('To view all items with a tag, click the number.'); ?></li>
            <li><?php echo __('To edit the tag name, click the name and begin editing, and hit "enter" to save. To cancel an edit, click the ESC key or click away from the tag.'); ?></li>
            <li><?php echo __('To delete a tag, click the X. Deleting a tag will not delete the tagged items.'); ?></li>
        </ol>
    </section>
    <section class="seven columns omega">
    <?php else: ?>
    <section>
    <?php endif; ?>
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

                echo "<span $class><a href=\"$uri\">$label</a></span>";
            }
            ?>
            <ul class="quick-filter-wrapper">
                <li><a href="#"><?php echo __('Record Types'); ?></a>
                <ul class="dropdown">
                    <li><span class="quick-filter-heading"><?php echo __('Record Types') ?></span></li>
                    <li><a href="<?php echo $this->url(); ?>"><?php echo __('All'); ?></a></li>
                    <?php foreach($record_types as $record_type): ?>
                    <li><a href="<?php echo url('tags', array('type' => $record_type)); ?>"><?php echo __($record_type); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                </li>
            </ul>
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
    </section>
    <?php echo $paginationLinks; ?>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>

<?php if($canEdit): ?>
<script type="text/javascript">
jQuery(document).ready(function () {
    var editableURL = '<?php echo url('tags/rename-ajax'); ?>';
    var tagURLBase = '<?php echo url('items/browse?tags='); ?>';
    var csrfToken = <?php echo js_escape($csrfToken); ?>;
    Omeka.Tags.enableEditInPlace(editableURL, tagURLBase, csrfToken);
});
</script>
<?php endif; ?>

<?php echo foot(); ?>

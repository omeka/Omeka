<?php
$canEdit = is_allowed('Tags', 'edit');
$canDelete = is_allowed('Tags', 'delete');

if ($canEdit):
    queue_js_file(array('vendor/jquery.jeditable', 'tags'));
    $pageTitle = __('Edit Tags');
else:
    $pageTitle = __('Browse Tags');
endif;

$pageTitle .= ' ' .  __('(%s total)', $total_tags);
echo head(array('title'=>$pageTitle,'bodyclass'=>'tags browse-tags'));
echo flash();

?>
<div id='search-filters'>
    <ul><li><?php echo __('Record Type') . ': ' . $browse_for; ?></li></ul>
</div>

<?php if ($total_tags): ?>
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
                __('Most') => array('sort_field' => 'count', 'sort_dir' => 'd'),
                __('Least') => array('sort_field' => 'count','sort_dir' => 'a'),
                __('Alphabetical') => array('sort_field' => 'name', 'sort_dir'=> 'a'),
                __('Recent') => array('sort_field' => 'time', 'sort_dir' => 'd')
            );

            foreach ($sortOptions as $label => $params) {
                $uri = html_escape(current_url($params));
                $class = ($sort == $params) ? ' class="current"' : '';

                echo "<span $class><a href=\"$uri\">$label</a></span>";
            }
            ?>
            <ul class="quick-filter-wrapper">
                <li><a href="#"><?php echo __('Record Types'); ?></a>
                <ul class="dropdown">
                    <li><span class="quick-filter-heading"><?php echo __('Record Types') ?></span></li>
                    <?php foreach($record_types as $record_type): ?>
                    <li><a href="<?php echo url('tags', array('tagType' => $record_type)); ?>"><?php echo __($record_type); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                </li>
            </ul>            
        </div>
        <ul class="tag-list">
        <?php foreach ($tags as $tag): ?>
            <li>
            <?php if($browse_for == 'Item'):?>
                <a href="<?php echo url('items/?tag=' . $tag->name); ?>" class="count"><?php echo $tag['tagCount']; ?></a>
            <?php else: ?>
                <span class="count"><?php echo $tag['tagCount']; ?></span>
            <?php endif; ?>
            <?php if ($canEdit): ?>
                <span class="tag edit-tag" id="<?php echo $tag->id; ?>"><?php echo $tag->name; ?></span>
            <?php else: ?>
                <span class="tag"><?php echo $tag->name; ?></span>
            <?php endif; ?>
            <?php if ($canDelete): ?> 
                <span class="delete-tag"><?php echo link_to($tag, 'delete-confirm', 'delete', array('class' => 'delete-confirm')); ?></span>
            <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php fire_plugin_hook('admin_tags_browse', array('tags' => $tags, 'view' => $this)); ?>
    </section>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>

<?php if($canEdit): ?>
<script type="text/javascript">
jQuery(document).ready(function () {
    var editableURL = '<?php echo url('tags/rename-ajax'); ?>';
    var tagURLBase = '<?php echo url('items/?tag='); ?>';
    var csrfToken = <?php echo js_escape($csrfToken); ?>;
    Omeka.Tags.enableEditInPlace(editableURL, tagURLBase, csrfToken);
});
</script>
<?php endif; ?>

<?php echo foot(); ?>

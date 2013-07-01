<?php
queue_js_file(array('vendor/jquery.jeditable', 'tags'));
$pageTitle = __('Edit Tags') . ' ' .  __('(%s total)', $total_tags);
echo head(array('title'=>$pageTitle,'bodyclass'=>'tags browse-tags'));
echo flash();

?>
<?php if(isset($_GET['tagType'])):?>
<div id='search-filters'>
    <ul><li><?php echo __(html_escape($_GET['tagType'] . 's')); ?></li></ul>
</div>
<?php endif; ?>

<?php if ($total_tags): ?>
    <section class="three columns alpha">
        <h2><?php echo __('Editing Tags'); ?></h2>
        
        <ol>
            <li><?php echo __('To view all items with a tag, click the number.'); ?></li>
            <li><?php echo __('To edit the tag name, click the name and begin editing, and hit "enter" to save. To cancel an edit, click the ESC key or click away from the tag.'); ?></li>
            <li><?php echo __('To delete a tag, click the X. Deleting a tag will not delete the tagged items.'); ?></li>
        </ol>
    </section>

    <section class="seven columns omega">
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
                <li><a href="#" tabindex="0"><?php echo __('Record Types'); ?></a>
                <ul class="dropdown">
                    <li><span class="quick-filter-heading"><?php echo __('Record Types') ?></span></li>
                    <?php foreach($record_types as $record_type): ?>
                    <li><a href="<?php echo url('tags', array('tagType' => $record_type)); ?>"><?php echo __($record_type . 's'); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                </li>
            </ul>            
        </div>
        <ul class="tag-list">
        <?php foreach ($tags as $tag): ?>
            <li>
                <a href="<?php echo url('items/?tag=' . $tag->name); ?>" class="count"><?php echo $tag['tagCount']; ?></a> 
                <span class="edit-tag" id="<?php echo $tag->id; ?>"><?php echo $tag->name; ?></span> 
                <span class="delete-tag"><?php echo link_to($tag, 'delete-confirm', 'delete', array('class' => 'delete-confirm')); ?></span>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php fire_plugin_hook('admin_tags_browse', array('tags' => $tags, 'view' => $this)); ?>
    </section>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function () {
    var editableURL = '<?php echo url('tags/rename-ajax'); ?>';
    var tagURLBase = '<?php echo url('items/?tag='); ?>';
    Omeka.Tags.enableEditInPlace(editableURL, tagURLBase);
});
</script>

<?php echo foot(); ?>

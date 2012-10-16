<?php
$pageTitle = __('Edit Tags') . ' ' .  __('(%s total)', $total_tags);
echo head(array('title'=>$pageTitle, 'content_class' => 'horizontal-nav','bodyclass'=>'tags browse-tags primary')); ?>
<?php echo flash(); ?>
<?php if ($total_tags): ?>

<div class="three columns alpha">

    <h2><?php echo __('Editing Tags'); ?></h2>
    
    <ol>
        <li><?php echo __('The numbers of items with this tag. Click the number to see all items with this tag.'); ?></li>
        <li><?php echo __('To edit the name of the tag, click the name and start typing. Clicking away will cancel the edit. Hitting "return" when finished editing will save the new tag name.'); ?></li>
        <li><?php echo __('Click here to delete the tag. Deleting the tag will not delete the tagged items.'); ?></li>
    </ol>

</div>

<div class="seven columns omega">

    <p id="tags-nav">
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
    </p>
    <div class="tag-list">
    <ul>
    <?php foreach ($tags as $tag): ?>
        <li>
            <a href="<?php echo url('items/?tag=' . $tag->name); ?>" class="count"><?php echo $tag['tagCount']; ?></a> 
            <span class="edit-tag" id="<?php echo $tag->id; ?>"><?php echo $tag->name; ?></span> 
            <span class="delete-tag"><?php echo link_to($tag, 'delete-confirm', 'delete', array('class' => 'delete-confirm')); ?></span>
            
        </li>
    <?php endforeach; ?>
    </ul>
    </div>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>

</div>

</div>

<script>

 jQuery(document).ready(function() {
     jQuery('.edit-tag').editable(<?php echo js_escape(url('tags/rename-ajax')); ?>); 
 });
 
</script>

<?php echo foot(); ?>

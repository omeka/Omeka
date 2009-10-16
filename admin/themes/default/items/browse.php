<?php head(array('title'=>'Browse Items','content_class' => 'horizontal-nav', 'bodyclass'=>'items primary browse-items')); ?>
<h1>Browse Items (<?php echo total_results();?> total)</h1>
<p id="add-item" class="add-button"><a class="add" href="<?php echo html_escape(uri('items/add')); ?>">Add an Item</a></p>

<?php if ( total_results() ): ?>

<ul id="section-nav" class="navigation<?php if(@$_GET['view'] == 'detailed') echo ' detailed'; if (empty($_GET['view']) || @$_GET['view'] == 'simple') echo ' simple'; ?>">
<?php
    $section_nav = array(
        'List View' => current_uri(array('view'=>'simple')), 
        'Detailed View' => current_uri(array('view'=>'detailed'))
        );
    
    $section_nav = apply_filters('admin_navigation_items_browse', $section_nav, $items);
                
    echo nav($section_nav);
?>
</ul>
<?php endif; ?>

<div id="primary">
    <?php if ( total_results() ): ?>
    <?php echo flash(); ?>
    
    <div id="browse-meta">
        <div id="simple-search-form">
            <?php echo simple_search(); ?>
            <span id="advanced-search-link"><?php echo link_to_advanced_search(); ?></span>
        </div>


        <div class="pagination"><?php echo pagination_links(); ?></div>
    </div>
    
<form id="items-browse" action="<?php echo html_escape(uri('items/power-edit')); ?>" method="post" accept-charset="utf-8">

<fieldset id="view-choice">
    <?php 
        switch ($_GET['view']) {
            case 'detailed':
                common('detailed-view', compact('items'), 'items');
                break;
            case 'simple':
            default:
                common('simple-view', compact('items'), 'items');
                break;
        }
     ?>
</fieldset>
<div class="pagination"><?php echo pagination_links(); ?></div>

<fieldset>
    <input type="submit" class="submit submit-medium" id="save-changes" name="submit" value="Save Changes" />
</fieldset>
</form>

<?php elseif(!total_items()): ?>
    <div id="no-items">
    <p>There are no items in the archive yet.
    
    <?php if(has_permission('Items','add')): ?>
          Why don&#8217;t you <?php echo link_to('items', 'add', 'add one'); ?>?</p>
    <?php endif; ?>
</div>
    
<?php else: ?>
    <h1>The query searched <?php echo total_items(); ?> items and returned no results.</h1>
<?php endif; ?>

<div>
    <h2>Output Formats</h2>
    <?php echo output_format_list(false); ?>
</div>

<?php fire_plugin_hook('admin_append_to_items_browse_primary', $items); ?>

</div>
<?php foot(); ?>

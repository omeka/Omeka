<?php head(array('title'=>'Browse Items','content_class' => 'horizontal-nav', 'bodyclass'=>'items primary browse-items')); ?>
<h1>Browse Items (<?php echo total_results();?> total)</h1>
<p id="add-item" class="add-button"><a class="add" href="<?php echo html_escape(uri('items/add')); ?>">Add an Item</a></p>

<?php if ( total_results() ): ?>

<?php 
$browseView = 'simple';
if (isset($_GET['view']) && $_GET['view'] == 'detailed'):
    $browseView = 'detailed';
endif; ?>
<ul id="section-nav" class="navigation <?php echo $browseView; ?>">
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
    <?php echo flash(); ?>
    <?php if ( total_results() ): ?>    
    <div id="browse-meta" class="group">
    	<div id="simple-search-form">
    	    <ul id="items-sort" class="navigation">
            <?php
                echo nav(array(
                    'All' => uri('items'), 
                    'Public' => uri('items/browse?public=1'),
                    'Private' => uri('items/browse?public=0'),
                    'Featured' => uri('items/browse?featured=1')
                    ));
            ?>
            </ul>
    		<?php echo simple_search(); ?>
    		<?php echo link_to_advanced_search('Advanced Search', array('id' => 'advanced-search-link')); ?>
    	</div>

        <div class="pagination"><?php echo pagination_links(); ?></div>
	</div>
    
<form id="items-browse" action="<?php echo html_escape(uri('items/power-edit')); ?>" method="post" accept-charset="utf-8">

<fieldset id="view-choice">
    <?php common("$browseView-view", compact('items'), 'items'); ?>
</fieldset>
<div class="pagination"><?php echo pagination_links(); ?></div>

<fieldset>
    <input type="submit" class="submit" id="save-changes" name="submit" value="Save Changes" />
</fieldset>
</form>

<div>
    <h2>Output Formats</h2>
    <?php echo output_format_list(false); ?>
</div>

<?php elseif(!total_items()): ?>
    <div id="no-items">
    <p>There are no items in the archive yet.
    
    <?php if(has_permission('Items','add')): ?>
          Why don&#8217;t you <?php echo link_to('items', 'add', 'add one'); ?>?</p>
    <?php endif; ?>
</div>
    
<?php else: ?>
    <p>The query searched <?php echo total_items(); ?> items and returned no results. Would you like to <?php echo link_to_advanced_search('refine your search'); ?>?</p>
    
<?php endif; ?>



<?php fire_plugin_hook('admin_append_to_items_browse_primary', $items); ?>

</div>
<?php foot(); ?>

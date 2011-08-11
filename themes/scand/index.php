<?php head(array('bodyid'=>'home', 'bodyclass' =>'two-col')); ?>
<div id="primary">
    <?php if ($homepageText = get_theme_option('Homepage Text')): ?>
    <p><?php echo $homepageText; ?></p>
    <?php endif; ?>
    
    <!-- Recent Items -->		
    <div id="recent-items">
        <h2>Recently Added Items</h2>
        <?php 
        $homepageRecentItems = (int)get_theme_option('Homepage Recent Items') ? get_theme_option('Homepage Recent Items') : '3';
        set_items_for_loop(recent_items($homepageRecentItems));
        if (has_items_for_loop()): 
        ?>
        <ul class="items-list">
        <?php while (loop_items()): ?>
        <li class="item">
            <h3><?php echo link_to_item(); ?></h3>
            <?php if($itemDescription = item('Dublin Core', 'Description', array('snippet'=>150))): ?>
                <p class="item-description"><?php echo $itemDescription; ?></p>
            <?php endif; ?>						
        </li>
        <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p>No recent items available.</p>
        <?php endif; ?>
        <p class="view-items-link"><?php echo link_to_browse_items('View All Items'); ?></p>
    </div><!-- end recent-items -->

    <?php if (get_theme_option('Display Featured Item') == 1): ?>
    <!-- Featured Item -->
    <div id="featured-item">
        <?php echo display_random_featured_item(); ?>
    </div><!--end featured-item-->	
    <?php endif; ?>

<!-- Featured Collection -->
<?php if (get_theme_option('Display Featured Collection') !== '0'): ?>
<div id="secondary">
    <?php if (get_theme_option('Display Featured Collection')): ?>
    <div id="featured-collection">
        <?php echo display_random_featured_collection(); ?>
    </div><!-- end featured collection -->
    <?php endif; ?>	
    <?php if ((get_theme_option('Display Featured Exhibit')) && function_exists('exhibit_builder_display_random_featured_exhibit')): ?>
    <!-- Featured Exhibit -->
    <?php echo exhibit_builder_display_random_featured_exhibit(); ?>
    <?php endif; ?>
    </div><!-- end recent-collections -->
</div><!-- end secondary -->
<?php endif; ?>

</div><!-- end primary -->
<?php foot(); ?>

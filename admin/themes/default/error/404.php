<?php 
    header ("HTTP/1.0 404 Not Found"); 
?>

<?php head(array('bodyclass'=>'error404 primary', 'title'=>'Page Not Found')); ?>
<h1>404: Page Not Found</h1>

<div id="primary" class="filenotfound">
    <?php echo flash(); ?>
    
    <p>You've tried to access a page that does not seem to exist. Sometimes this happens. Below is a quick break-down of the items, collections, and exhibits on this site. If this does not help, try contacting the <a href="mailto:<?php echo settings('administrator_email'); ?>">site administrator</a>.</p>
    
    <div id="recent-items">
        <h2>Recent Items</h2>
        <ul class="items">
            <?php 
            set_items_for_loop(recent_items('10'));
            if(has_items_for_loop()): while(loop_items()): ?>
            <li class="item"><?php echo link_to_item(); ?></li>
            <?php endwhile; endif; ?>
        </ul>
    </div>
    <div id="recent-collections">
        <h2>Recent Collections</h2>
        <ul class="collections">
            <?php 
            set_collections_for_loop(recent_collections('5'));
            if(has_collections_for_loop()): while (loop_collections()):?>
            <li class="collection"><?php echo link_to_collection(); ?></li>
            <?php endwhile; endif; ?>
        </ul>
    </div>

</div>

<?php foot(); ?>

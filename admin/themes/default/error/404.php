<?php 
$pageTitle = __('404: Page Not Found');
head(array('bodyclass'=>'error404 primary', 'title'=> $pageTitle)); ?>
<h1><?php echo $pageTitle; ?></h1>

<div id="primary" class="filenotfound">
    <?php echo flash(); ?>
    
    <p><?php echo __('You&#8217;ve tried to access a page that does not seem to exist. Sometimes this happens. Below is a quick break-down of the items, collections, and exhibits on this site.'); ?></p>
    <p>
    <?php 
    $siteAdministratorEmail = '<a href="mailto:'. settings('administrator_email') . '">' . __('site administrator') . '</a>';
    echo __('If this does not help, try contacting the %s.', $siteAdministratorEmail); ?></p>
    <div id="recent-items">
        <h2><?php echo __('Recent Items'); ?></h2>
        <ul class="items">
            <?php 
            set_items_for_loop(recent_items('10'));
            if(has_items_for_loop()): while(loop_items()): ?>
            <li class="item"><?php echo link_to_item(); ?></li>
            <?php endwhile; endif; ?>
        </ul>
    </div>
    <div id="recent-collections">
        <h2><?php echo __('Recent Collections'); ?></h2>
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

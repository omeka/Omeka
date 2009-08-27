<?php 
    $navArray = array();
    $navArray['Browse Tags'] = uri('tags/browse');
    if(has_permission('Tags','edit')) {
        $navArray['Edit Tag'] = uri('tags/edit');
    }
    if(has_permission('Tags','delete')) {
        $navArray['Delete Tag'] = uri('tags/delete');
    }
    $navArray = apply_filters('admin_navigation_tags', $navArray);
    ?>
    <ul id="section-nav" class="navigation">
    <?php echo nav($navArray); ?>
    </ul>
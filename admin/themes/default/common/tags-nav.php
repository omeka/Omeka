<?php 
    $navArray = array();
    $navArray[__('Browse Tags')] = url('tags/browse');
    if(is_allowed('Tags','edit')) {
        $navArray[__('Edit Tag')] = url('tags/edit');
    }
    if(is_allowed('Tags','delete')) {
        $navArray[__('Delete Tag')] = url('tags/delete');
    }
    $navArray = apply_filters('admin_navigation_tags', $navArray);
    ?>
    <ul id="section-nav" class="navigation">
    <?php echo nav($navArray); ?>
    </ul>
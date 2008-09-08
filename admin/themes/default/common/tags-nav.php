<?php 
    $navArray = array();
    $navArray['Browse Tags'] = uri('tags/browse');
    if(has_permission('Tags','edit')) {
        $navArray['Edit a Tag'] = uri('tags/edit');
    }
    if(has_permission('Tags','delete')) {
        $navArray['Delete a Tag'] = uri('tags/delete');
    }
    ?>
    <ul id="section-nav" class="navigation">
    <?php echo nav($navArray); ?>
    </ul>
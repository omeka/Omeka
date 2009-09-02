<ul class="primary-nav navigation">
<?php
    $primaryNav = array(
        'Items' => uri('items'), 
        'Collections' => uri('collections'),
        'Item Types' => uri('item-types'),
        'Tags' => uri('tags')
        );              
    echo nav(apply_filters('admin_navigation_main', $primaryNav));
?>
</ul>
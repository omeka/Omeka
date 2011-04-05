<div id="primary-nav-container">
<ul class="primary-nav navigation">
<?php
    $primaryNav = array(
        __('Items') => uri('items'), 
        __('Collections') => uri('collections'),
        __('Item Types') => uri('item-types'),
        __('Tags') => uri('tags')
        );              
    echo nav(apply_filters('admin_navigation_main', $primaryNav));
?>
</ul>
</div>

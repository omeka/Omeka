<?php if(has_permission('Plugins','edit')): ?>
<ul id="global-nav">
<?php
    $globalNav = array();
    if(has_permission('Plugins', 'edit')) {
        $globalNav[__('Plugins')] = uri('plugins');
    }
    if(has_permission('Themes', 'edit')) {
        $globalNav[__('Themes')] = uri('themes');
    }
    if(has_permission('Users', 'edit')) {
        $globalNav[__('Users')] = uri('users');
    }
    if(has_permission('Settings', 'edit')) {
        $globalNav[__('Settings')] = uri('settings');
    }
    echo nav(apply_filters('admin_navigation_global', $globalNav));
?>    
</ul>
<?php endif; ?>
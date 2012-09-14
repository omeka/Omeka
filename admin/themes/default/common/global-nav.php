<?php if(has_permission('Plugins','edit')): ?>
<ul id="global-nav">
<?php
    $globalNav = array();
    if(has_permission('Plugins', 'edit')) {
        $globalNav[__('Plugins')] = url('plugins');
    }
    if(has_permission('Themes', 'edit')) {
        $globalNav[__('Themes')] = url('themes');
    }
    if(has_permission('Users', 'edit')) {
        $globalNav[__('Users')] = url('users');
    }
    if(has_permission('Settings', 'edit')) {
        $globalNav[__('Settings')] = url('settings');
    }
    echo nav(apply_filters('admin_navigation_global', $globalNav));
?>    
</ul>
<?php endif; ?>
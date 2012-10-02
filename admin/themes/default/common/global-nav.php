<?php if(is_allowed('Plugins','edit')): ?>
<ul id="global-nav">
<?php
    $globalNav = array();
    if(is_allowed('Plugins', 'edit')) {
        $globalNav[__('Plugins')] = url('plugins');
    }
    if(is_allowed('Appearance', 'edit')) {
        $globalNav[__('Appearance')] = url('appearance');
    }
    if(is_allowed('Users', 'edit')) {
        $globalNav[__('Users')] = url('users');
    }
    if(is_allowed('Settings', 'edit')) {
        $globalNav[__('Settings')] = url('settings');
    }
    echo nav(apply_filters('admin_navigation_global', $globalNav));
?>    
</ul>
<?php endif; ?>
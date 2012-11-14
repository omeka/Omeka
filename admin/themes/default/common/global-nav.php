<?php if(is_allowed('Plugins','edit')): ?>
<div id="global-nav">
<?php
    $globalNav = array(
        array(
            'label' => __('Plugins'),
            'uri' => url('plugins'),
            'resource' => 'Plugins',
            'privilege' => 'edit',
            'theme' => 'admin',
            ),
        array(
            'label' => __('Appearance'),
            'uri' => url('appearance'),
            'resource' => 'Appearance',
            'privilege' => 'edit',
            'theme' => 'admin'
            ),
        array(
            'label' => __('Users'),
            'uri' => url('users'),
            'resource' => 'Users',
            'privilege' => 'edit',
            'theme' => 'admin'
            ),
        array(
            'label' => __('Settings'),
            'uri' => url('settings'),
            'resource' => 'Settings',
            'privilege' => 'edit',
            'theme' => 'admin'
            )
        );
    echo nav($globalNav, 'admin_navigation_global');
?>    
</div>
<?php endif; ?>

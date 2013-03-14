<div id="global-nav">
<?php
    $globalNav = array(
        array(
            'label' => __('Plugins'),
            'uri' => url('plugins'),
            'resource' => 'Plugins',
            'privilege' => 'edit'
            ),
        array(
            'label' => __('Appearance'),
            'uri' => url('appearance'),
            'resource' => 'Appearance',
            'privilege' => 'edit'
            ),
        array(
            'label' => __('Users'),
            'uri' => url('users'),
            'resource' => 'Users',
            'privilege' => 'edit'
            ),
        array(
            'label' => __('Settings'),
            'uri' => url('settings'),
            'resource' => 'Settings',
            'privilege' => 'edit'
            )
        );
    echo nav($globalNav, 'admin_navigation_global');
?>    
</div>

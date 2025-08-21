<?php
    $globalNav = [
        [
            'label' => __('Plugins'),
            'uri' => url('plugins'),
            'resource' => 'Plugins',
            'privilege' => 'edit'
            ],
        [
            'label' => __('Appearance'),
            'uri' => url('appearance'),
            'resource' => 'Appearance',
            'privilege' => 'edit'
            ],
        [
            'label' => __('Users'),
            'uri' => url('users'),
            'resource' => 'Users',
            'privilege' => 'edit'
            ],
        [
            'label' => __('Settings'),
            'uri' => url('settings'),
            'resource' => 'Settings',
            'privilege' => 'edit'
            ]
        ];
    echo nav($globalNav, 'admin_navigation_global');
?>


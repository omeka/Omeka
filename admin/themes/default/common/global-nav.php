<button type="button" class="mobile-menu collapse" aria-expanded="true" data-target="#global-nav"><?php echo __('Site Management'); ?></button>
<nav id="global-nav" class="in" aria-label="<?php echo __('Site Management'); ?>">
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
            'privilege' => 'edit',
            'pages' => [
                [
                    'label' => __('Security'),
                    'uri' => url('settings/edit-security'),
                    'resource' => 'Settings',
                    'privilege' => 'edit'
                ],
                [
                    'label' => __('Search'),
                    'uri' => url('settings/edit-search'),
                    'resource' => 'Settings',
                    'privilege' => 'edit'
                ],
                [
                    'label' => __('Element Sets'),
                    'uri' => url('element-sets'),
                    'resource' => 'Settings',
                    'privilege' => 'edit'
                ],
                [
                    'label' => __('Item Type Elements'),
                    'uri' => url('settings/edit-item-type-elements'),
                    'resource' => 'Settings',
                    'privilege' => 'edit'
                ],
                [
                    'label' => __('API'),
                    'uri' => url('settings/edit-api'),
                    'resource' => 'Settings',
                    'privilege' => 'edit'
                ]
            ]
            ]
        ];
    echo nav($globalNav, 'admin_navigation_global');
?>
</nav>
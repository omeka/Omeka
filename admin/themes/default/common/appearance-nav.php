<nav id="section-nav" class="navigation vertical" role="navigation">
<?php
    $navArray = [
        [
            'label' => __('Themes'),
            'uri' => url('themes'),
            'resource' => 'Themes',
            'privilege' => 'edit'
        ],
        [
            'label' => __('Navigation'),
            'uri' => url('appearance/edit-navigation')
        ],
        [
            'label' => __('Settings'),
            'uri' => url('appearance/edit-settings')
        ],
    ];
    echo nav($navArray, 'admin_navigation_appearance');
?>
</nav>

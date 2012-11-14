<ul id="section-nav" class="navigation vertical">
<?php
    $navArray = array(
        array(
            'label' => __('Themes'),
            'uri' => url('themes'),
            'resource' => 'Themes',
            'privilege' => 'edit',
            'theme' => 'admin'
        ),
        array(
            'label' => __('Navigation'),
            'uri' => url('appearance/edit-navigation'),
            'theme' => 'admin'
        ),
        array(
            'label' => __('Settings'),
            'uri' => url('appearance/edit-settings'),
            'theme' => 'admin'
        ),
    );
    echo nav($navArray, 'admin_navigation_settings');
?>
</ul>

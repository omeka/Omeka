<nav id="section-nav" class="navigation vertical" role="navigation">
<?php
    $navArray = array(
        array(
            'label' => __('Themes'),
            'uri' => url('themes'),
            'resource' => 'Themes',
            'privilege' => 'edit'
        ),
        array(
            'label' => __('Navigation'),
            'uri' => url('appearance/edit-navigation')
        ),
        array(
            'label' => __('Settings'),
            'uri' => url('appearance/edit-settings')
        ),
    );
    echo nav($navArray, 'admin_navigation_appearance');
?>
</nav>

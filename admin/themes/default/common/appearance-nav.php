<ul id="section-nav" class="navigation vertical">
<?php
    $navArray = array(
        array(
            'label' => __('Themes'),
            'uri' => url('themes'),
            'resource' => 'Themes',
            'privilege' => 'edit'
        ),
        array(
            'label' => __('Appearance Settings'),
            'uri' => url('appearance/edit-appearance')
        ),
        array(
            'label' => __('Navigation'),
            'uri' => url('appearance/edit-navigation')
        ),
    );
    echo nav($navArray, 'admin_navigation_settings');
?>
</ul>

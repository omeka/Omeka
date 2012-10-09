<?php // if (is_allowed('Settings', 'edit')): ?>
<ul id="section-nav" class="navigation vertical">
<?php
    $navArray = array(
        array(
            'label' => __('Appearance Settings'),
            'uri' => url('appearance')
        ),
        array(
            'label' => __('Themes'),
            'uri' => url('themes'),
            'resource' => 'Themes',
            'privilege' => 'edit'
        ),
        array(
            'label' => __('Navigation'),
            'uri' => url('navigation')
        )
    );

    echo nav($navArray, 'admin_navigation_settings');
?>
</ul>
<?php // endif; ?>

<?php if (is_allowed('Settings', 'edit')): ?>
<div id="section-nav">
<?php
    $navArray = array(
        array(
            'label' => __('General Settings'),
            'controller' => 'settings'
        ),
        array(
            'label' => __('Element Sets'),
            'controller' => 'element-sets',
            'resource' => 'ElementSets',
            'privilege' => 'browse'
        ),
        array(
            'label' => __('Security Settings'),
            'controller' => 'security',
            'resource' => 'Security',
            'privilege' => 'edit'
        ),
        array(
            'label' => __('Search Settings'),
            'controller' => 'search',
            'action' => 'settings',
        )
    );

    echo nav($navArray, 'admin_navigation_settings');
?>
</div>
<?php endif ?>

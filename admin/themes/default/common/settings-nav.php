<?php if (is_allowed('Settings', 'edit')): ?>
<div id="section-nav">
<?php
    $navArray = array(
        array(
            'label' => __('General'),
            'uri' => url('settings/edit-settings'),
            'theme' => 'admin'
        ),
        array(
            'label' => __('Security'),
            'uri' => url('settings/edit-security'),
            'resource' => 'Security',
            'privilege' => 'edit',
            'theme' => 'admin'
        ),
        array(
            'label' => __('Search'),
            'uri' => url('settings/edit-search'),
            'theme' => 'admin'
        ),
        array(
            'label' => __('Element Sets'),
            'uri' => url('element-sets'),
            'resource' => 'ElementSets',
            'privilege' => 'browse',
            'theme' => 'admin'
        ),
    );
    echo nav($navArray, 'admin_navigation_settings');
?>
</div>
<?php endif ?>

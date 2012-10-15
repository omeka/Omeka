<?php if (is_allowed('Settings', 'edit')): ?>
<div id="section-nav">
<?php
    $navArray = array(
        array(
            'label' => __('General Settings'),
            'uri' => url('settings')
        ),
        array(
            'label' => __('Element Sets'),
            'uri' => url('element-sets'),
            'resource' => 'ElementSets',
            'privilege' => 'browse'
        ),
        array(
            'label' => __('Security Settings'),
            'uri' => url('security'),
            'resource' => 'Security',
            'privilege' => 'edit'
        ),
        array(
            'label' => __('Search Settings'),
            'uri' => url('search/settings')
        )
    );

    echo nav($navArray, 'admin_navigation_settings');
?>
</div>
<?php endif ?>

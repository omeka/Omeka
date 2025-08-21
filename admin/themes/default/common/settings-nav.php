<?php if (is_allowed('Settings', 'edit')): ?>
<div id="section-nav" role="navigation" class="navigation">
<?php
    $navArray = [
        [
            'label' => __('General'),
            'uri' => url('settings/edit-settings')
        ],
        [
            'label' => __('Security'),
            'uri' => url('settings/edit-security'),
            'resource' => 'Security',
            'privilege' => 'edit'
        ],
        [
            'label' => __('Search'),
            'uri' => url('settings/edit-search')
        ],
        [
            'label' => __('Element Sets'),
            'uri' => url('element-sets'),
            'resource' => 'ElementSets',
            'privilege' => 'browse'
        ],
        [
            'label' => __('Item Type Elements'),
            'uri' => url('settings/edit-item-type-elements'),
            'resource' => 'ElementSets',
            'privilege' => 'browse'
        ],
        [
            'label' => __('API'),
            'uri' => url('settings/edit-api')
        ],
    ];
    echo nav($navArray, 'admin_navigation_settings');
?>
</div>
<?php endif ?>

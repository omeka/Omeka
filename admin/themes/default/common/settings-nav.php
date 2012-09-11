<?php if (has_permission('Settings', 'edit')): ?>
<ul id="section-nav" class="navigation vertical">
<?php
    $navArray = array();

    $navArray[__('General Settings')] = url('settings');
    if (has_permission('ElementSets', 'browse')) {
        $navArray[__('Element Sets')] = url('element-sets');
    }
    if (has_permission('Security', 'edit')) {
        $navArray[__('Security Settings')] = url('security');
    }
    $navArray[__('Search Settings')] = url('search/settings');

    echo nav(apply_filters('admin_navigation_settings', $navArray));
?>
</ul>
<?php endif ?>

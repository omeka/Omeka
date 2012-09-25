<?php if (is_allowed('Settings', 'edit')): ?>
<ul id="section-nav" class="navigation vertical">
<?php
    $navArray = array();

    $navArray[__('General Settings')] = url('settings');
    if (is_allowed('ElementSets', 'browse')) {
        $navArray[__('Element Sets')] = url('element-sets');
    }
    if (is_allowed('Security', 'edit')) {
        $navArray[__('Security Settings')] = url('security');
    }
    $navArray[__('Search Settings')] = url('search/settings');

    echo nav(apply_filters('admin_navigation_settings', $navArray));
?>
</ul>
<?php endif ?>

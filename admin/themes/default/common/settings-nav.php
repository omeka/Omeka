<?php if (has_permission('Settings', 'edit')): ?>
<ul id="section-nav" class="navigation vertical">
<?php
    $navArray = array();

    $navArray[__('General Settings')] = uri('settings');
    if (has_permission('ElementSets', 'browse')) {
        $navArray[__('Element Sets')] = uri('element-sets');
    }
    if (has_permission('Security', 'edit')) {
        $navArray[__('Security Settings')] = uri('security');
    }

    echo nav(apply_filters('admin_navigation_settings', $navArray));
?>
</ul>
<?php endif ?>

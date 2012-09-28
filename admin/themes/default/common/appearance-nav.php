<?php // if (is_allowed('Settings', 'edit')): ?>
<ul id="section-nav" class="navigation vertical">
<?php
    $navArray = array();

    $navArray[__('Appearance Settings')] = url('appearance');

    if (is_allowed('Themes', 'edit')) {
        $navArray[__('Themes')] = url('themes');
    }
    $navArray[__('Navigation')] = url('navigation');

    echo nav(apply_filters('admin_navigation_settings', $navArray));
?>
</ul>
<?php // endif; ?>

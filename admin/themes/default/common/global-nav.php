<ul id="global-nav">
<?php
    $globalNav = array(
        __('Plugins') => uri('plugins'),
        __('Themes') => uri('themes'),
        __('Users') => uri('users'),
        __('Settings') => uri('settings')
        );
    echo nav(apply_filters('admin_navigation_global', $globalNav));
?>    
</ul>
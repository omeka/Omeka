<?php if (has_permission('Settings', 'edit')): ?>
<ul id="section-nav" class="navigation vertical">
<?php 
    $navArray = array();
    $navArray['General Settings'] = uri('settings');
    $navArray['Plugins'] = uri('plugins');
    $navArray['Themes'] = uri('themes');    
    if (has_permission('Users','browse')) {
    	$navArray['Users'] = uri('users');
    }
    $navArray['Element Sets'] = uri('element-sets');
    $navArray['Security Settings'] = uri('security');
    
	echo nav(apply_filters('admin_navigation_settings', $navArray)); 
?>
</ul>
<?php endif ?>

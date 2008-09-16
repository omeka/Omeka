<ul id="section-nav" class="navigation vertical">
<?php $navArray = array('General Settings' => uri('settings'),
                    'Element Sets' => uri('element-sets'),
					'Themes' => uri('themes'),
					'Plugins'=>uri('plugins'));
		
    if(has_permission('Users','browse') ) {
    	$navArray['Users'] = uri('users/browse');
    }
	echo nav(apply_filters('admin_navigation_settings', $navArray)); ?>
</ul>
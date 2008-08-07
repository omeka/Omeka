<ul id="secondary-nav" class="navigation">
<?php $navArray = array('General Settings' => url_for('settings'),
                    'Element Sets' => url_for('element-sets'),
					'Themes' => url_for('themes'),
					'Plugins'=>url_for('plugins'));
		
    if(has_permission('Users','browse') ) {
    	$navArray['Users'] = uri('users/browse');
    }
	echo nav(apply_filters('admin_navigation_settings', $navArray)); ?>
</ul>
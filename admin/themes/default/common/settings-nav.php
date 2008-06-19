<ul id="secondary-nav" class="navigation">
<?php $navArray = array('General' => uri('settings'),
					'Themes' => uri('themes'),
					'Plugins'=>uri('plugins')
					);
	echo nav(apply_filters('admin_navigation_settings', $navArray)); ?>
</ul>
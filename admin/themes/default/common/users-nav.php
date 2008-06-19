<ul id="secondary-nav" class="navigation">
	<?php 
	    $navArray = array();
		if(has_permission('Users','add')) {
			$navArray = array('Browse Users' => uri('users/browse'), 'User Roles' => uri('users/roles'));
		}
		echo nav(apply_filters('admin_navigation_users', $navArray));
	?>
</ul>
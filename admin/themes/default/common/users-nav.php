<ul id="secondary-nav" class="navigation">
	<?php 
		if(has_permission('Users','add')) {
			nav(array('Browse Users' => uri('users/browse'), 'User Roles' => uri('users/roles')));
		}
		fire_plugin_hook('load_navigation', 'users');	
	?>
</ul>
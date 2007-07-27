<ul id="secondary-nav" class="navigation">
	<?php 
		if(has_permission('Users','add')) {
			nav(array('Browse Users' => uri('users/browse'), 'Add User' => uri('users/add'), 'User Roles' => uri('users/roles')));
		}
	?>
</ul>
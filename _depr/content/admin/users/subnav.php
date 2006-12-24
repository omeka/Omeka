<ul id="sub-navigation" class="navigation subnav">
	<li<?php if(self::$_route['template'] == 'all') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'users' ); ?>">Show Users</a></li>
	<li<?php if(self::$_route['template'] == 'add') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'users', 'add' ); ?>">Add User</a></li>
</ul>

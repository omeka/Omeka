<ul id="sub-navigation" class="navigation subnav">
	<li <?php if(self::$_route['template'] == 'index') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'account' ); ?>">My Home</a></li>
	<li <?php if(self::$_route['template'] == 'items') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'account', 'items' ); ?>">My Items</a></li>
	<li <?php if(self::$_route['template'] == 'favorites') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'account', 'favorites' ); ?>">My Favorites</a></li>
	<li <?php if(self::$_route['template'] == 'tags') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'account', 'tags' ); ?>">My Tags</a></li>
	<li <?php if(self::$_route['template'] == 'edit') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'account', 'edit' ); ?>">My Profile</a></li>
</ul>

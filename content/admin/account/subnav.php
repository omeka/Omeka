<div id="sub-navigation">
<ul>
	<li <?php if(self::$_route['template'] == 'index') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'account' ); ?>">My Home</a></li>
	<li <?php if(self::$_route['template'] == 'contributions') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'account', 'contributions' ); ?>">Contributions</a></li>
	<li <?php if(self::$_route['template'] == 'favorites') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'account', 'favorites' ); ?>">Favorites</a></li>
	<li <?php if(self::$_route['template'] == 'tags') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'account', 'tags' ); ?>">Tags</a></li>
	<li <?php if(self::$_route['template'] == 'edit') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'account', 'edit' ); ?>">Settings</a></li>
</ul>
</div>
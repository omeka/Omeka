<div id="sub-navigation">
<ul class="navigation subnav">
<li <?php if(self::$_route['template'] == 'all') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'categories', 'all' ); ?>">View</a></li>
<li <?php if(self::$_route['template'] == 'add') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'categories', 'add' ); ?>">Add</a></li>
</ul>
</div>
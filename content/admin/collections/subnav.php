<ul id="sub-navigation" class="subnav navigation">
<li <?php if(self::$_route['template'] == 'all') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'collections', 'all' ); ?>">View</a></li>
<li <?php if(self::$_route['template'] == 'add') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'collections', 'add' ); ?>">Add</a></li>
</ul>

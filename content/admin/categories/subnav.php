<div id="sub-navigation">
<ul>
<li <?php if(self::$_route['template'] == 'all') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'categories', 'all' ); ?>">View</a></li>
<li <?php if(self::$_route['template'] == 'add') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'categories', 'add' ); ?>">Add</a></li>
</ul>
</div>
<div id="sub-navigation">
<ul>
<li <?php if(self::$_route['template'] == 'all') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'collections', 'all' ); ?>">View</a></li>
<li <?php if(self::$_route['template'] == 'add') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'collections', 'add' ); ?>">Add</a></li>
</ul>
</div>
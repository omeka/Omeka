<div id="sub-navigation">
<ul class="navigation subnav">
<li <?php if(self::$_route['template'] == 'all') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'types', 'all' ); ?>">Show Types</a></li>
<li <?php if(self::$_route['template'] == 'add') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'types', 'add' ); ?>">Add Type</a></li>
</ul>
</div>
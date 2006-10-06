<ul id="sub-navigation" class="subnav navigation">
<li <?php if(self::$_route['template'] == 'show') { echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'items', 'show/'  . $item->getId()); ?>">Show Item</a></li>

	<?php if( self::$_session->isAdmin()): ?>
	<li <?php if(self::$_route['template'] == 'edit') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'items', 'edit/' . $item->getId() ); ?>">Edit</a></li>
	<?php endif; ?>
</ul>
<ul id="sub-navigation" class="subnav navigation">
<li <?php if(self::$_route['template'] == 'show') { echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'objects', 'show/'  . $object->getId()); ?>">Show Object</a></li>

	<?php if( self::$_session->isAdmin()): ?>
	<li <?php if(self::$_route['template'] == 'edit') {echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'objects', 'edit' ); echo $object->getId(); ?>">Edit</a></li>
	<?php endif; ?>
</ul>
<div id="sub-navigation">
	<ul>

	<li <?php if(self::$_route['template'] == 'all') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'objects', 'all' ); ?>">View</a></li>
	<li <?php if(self::$_route['template'] == 'add') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'objects', 'add' ); ?>">Add</a></li>

	<?php if( self::$_session->isAdmin() &&  self::$_route['template'] == 'show' ): ?>
	<li <?php if(self::$_route['template'] == 'edit') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'objects', 'edit' ); echo $object->getId(); ?>">Edit</a></li>
	<?php endif; ?>
	</ul>
</div>
<ul id="sub-navigation" class="subnav navigation">

<li <?php if(self::$_route['template'] == 'show') { echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'objects', 'show/'); ; echo @$object->getId(); ?>">Show Item</a></li>

<?php if( self::$_session->isAdmin()): ?>
	<li <?php if(self::$_route['template'] == 'edit') { echo 'class="current"';} ?>><a href="<?php echo $_link->to( 'objects', 'edit' ); echo @$object->getId(); ?>">Edit Item</a></li>
<?php endif; ?>

</ul>
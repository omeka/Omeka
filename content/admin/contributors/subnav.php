<div id="sub-navigation">
<ul>
<li <?php if(self::$_route['template'] == 'all') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'contributors', 'all' ); ?>">View</a></li>
<li <?php if(self::$_route['template'] == 'add') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'contributors', 'add' ); ?>">Add</a></li>
<?php if( self::$_session->getUser()->getPermissions() <= 10 &&  self::$_route['template'] == 'show' ): ?>
<li <?php if(self::$_route['template'] == 'edit') {echo 'class="selected"';} ?>><a href="<?php echo $_link->to( 'contributors', 'edit' ); echo $contributor->getId(); ?>">Edit</a></li>
<?php endif; ?>
</ul>
</div>
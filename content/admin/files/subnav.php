<ul id="filenav" class="subnav navigation">
	<li <?php if(self::$_route['template'] == 'show') {echo 'class="current"';} ?>><a href="<?php echo $_link->to('files','show') . $file->getId(); ?>">Show File Metadata</a></li>
	<li <?php if(self::$_route['template'] == 'edit') {echo 'class="current"';} ?>><a href="<?php echo $_link->to('files', 'edit') . $file->getId(); ?>">Edit File Metadata</a></li>
	<li><a href="<?php echo $_link->to('objects','show') . $object->getId(); ?>">Back to Object Page</a></li>
</ul>
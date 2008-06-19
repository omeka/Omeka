<ul id="secondary-nav" class="navigation">
	<?php $navArray = array('Items' => uri('items'),
					'Types' => uri('types'),
					'Collections'=>uri('collections'),
					'Tags' => uri('tags'));
	echo nav(apply_filters('admin_navigation_archive', $navArray));	?>
</ul>
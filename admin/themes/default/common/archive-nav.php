<ul id="secondary-nav" class="navigation">
	<?php echo nav(array('Items' => uri('items'),
					'Types' => uri('types'),
					'Collections'=>uri('collections'),
					'Tags' => uri('tags')
					));
	fire_plugin_hook('load_navigation', 'archive');				
	?>
</ul>
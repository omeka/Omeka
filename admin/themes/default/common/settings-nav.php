<ul id="secondary-nav" class="navigation">
<?php echo nav(array('General' => uri('settings'),
					'Themes' => uri('themes'),
					'Plugins'=>uri('plugins')
					));
	fire_plugin_hook('load_navigation', 'settings');					
				?>
</ul>
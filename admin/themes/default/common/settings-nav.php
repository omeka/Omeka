<ul id="secondary-nav" class="navigation">
<?php admin_nav(array('General' => uri('settings'),
					'Themes' => uri('themes'),
					'Plugins'=>uri('plugins')
					));
	fire_plugin_hook('load_navigation', 'settings');					
				?>
</ul>
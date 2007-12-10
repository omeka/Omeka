<?php 

class PluginBroker 
{
	protected $_callbacks = array();
	
	private static $_instance;

	//Currently installed plugins
	protected $_installed = array();
	
	//Currently activated plugins
	protected $_active = array();
	
	//All plugins available in 'plugins' directory
	protected $_all = array();
	
	//The name of the current plugin (when used for hooks)
	protected $_current;
	
	//When we've just inserted a row into the plugins table, this will contain the ID
	protected $_last_insert_id;
	
	//Theme directories that have been added by plugins
	protected $_theme_dirs = array('public'=>array(),'admin'=>array());
	
	//Any navigation elements that have been added via plugins
	protected $_nav = array();
	
	//The data feeds that a plugin adds to the application
	protected $_feeds = array();
	
	public function __construct() 
	{
		Zend_Registry::set('plugin_broker', $this);
		
		//Construct the current list of potential, installed & active plugins
		require_once 'VersionedDirectoryIterator.php';
		
		//Loop through all the plugins in the plugin directory
		$dir = new VersionedDirectoryIterator(PLUGIN_DIR);
		$names = $dir->getValid();
		
		$this->_all = $names;

		//Get the list of currently installed plugins
		if(empty($this->_installed)) {
			$installed = array();
			$active = array();
			
			$db = get_db();
			
			$res = $db->query("SELECT p.name, p.active FROM $db->Plugin p");
			foreach ($res->fetchAll() as $row) {
				$installed[$row['name']] = $row['name'];
				
				//Only active plugins should be require'd
				if($row['active']) {
					$name = $row['name'];
					
					$this->setCurrentPlugin($name);
					
					$active[$name] = $name;
					
					//Require the file that contains the plugin
					$path = $this->getPluginFilePath($name);
					if(file_exists($path)) {
						require_once $path;
					}
				}
			}
			$this->_active = $active;
			$this->_installed = $installed;
		}
		
		//Fire all the 'initialize' hooks for the plugins
		$this->initialize();
	}
	
	protected function getPluginFilePath($name)
	{
		return PLUGIN_DIR . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'plugin.php';
	}
		
	/**
	 * Check if the plugin is active, then enable the hook for it
	 *
	 * @return void
	 **/
	public function addHook($hook, $callback)
	{	
		$current = $this->getCurrentPlugin();
					
		$this->_callbacks[$hook][$current] = $callback;
	}
	
	public function getHook($plugin, $hook)
	{		
		if(is_array($this->_callbacks[$hook])) {
			return $this->_callbacks[$hook][$plugin];
		}
	}
	
	/**
	 * The plugin helper functions do not have any way of determining what plugin to is currently in focus
	 * These get/setCurrentPlugin methods allow the broker to know how to delegate to specific plugins if necessary
	 *
	 * @return string
	 **/
	protected function setCurrentPlugin($plugin) {
		$this->_current = $plugin;
	}
	
	protected function getCurrentPlugin() {
		return $this->_current;
	}
	
	public function isActive($plugin) {
		return in_array($plugin, $this->_active);
	}
	
	public function isInstalled($plugin) {
		return in_array($plugin, $this->_installed);
	}
	
	public function getAll()
	{
		return $this->_all;
	}
	
	/**
	 * Return a list of plugins that have not been installed yet
	 *
	 * @return void
	 **/
	public function getNew()
	{
		$new = array_diff($this->_all, array_keys($this->_installed));
		
		return $new;
	}
	
	public function config($plugin)
	{
		//Check if the POST is empty, then check for a configuration form	
		if(empty($_POST)) {

			$config_form_hook = $this->getHook($plugin, 'config_form');
	
			//If there is a configuration form available, load that and return the output for rendering later
			if($config_form_hook) {
				require_once HELPERS;
				
				ob_start();
				call_user_func_array($config_form_hook, array($_POST)); 
				$config = ob_get_clean();	
		
				return $config;					
			}
		}
		//Data has been POSTed to the configuration mechanism
		else {
			//Run the 'config' hook, then run the rest of the installer
			$config_hook = $this->getHook($plugin, 'config');
			
			if($config_hook) {
				call_user_func_array($config_hook, array($_POST));
			}			
		}		
	}
	
	public function install($plugin) 
	{
		
		//Make sure the plugin helpers like define_metafield() etc. that need to be aware of scope can operate on this plugin
		$this->setCurrentPlugin($plugin);
		
		//Include the plugin file manually because it was not included via the constructor
		$file = $this->getPluginFilePath($plugin);
		if(file_exists($file)) {
			require_once $file;
		}
		else {
			throw new Exception( "Plugin named '$plugin' requires at minimum a file named 'plugin.php' to exist.  Please add this file or remove the '$plugin' directory." );
		}
	
		$config = $this->config($plugin);
		
		if($config !== null) {
			return $config;		
		}
								
		//Now run the installer for the plugin
		$install_hook = $this->_callbacks['install'][$plugin];

		try {
			$db = get_db();
			
			//Insert the plugin into the DB
			$db->exec("INSERT IGNORE INTO $db->Plugin (name, active) VALUES ('$plugin', 1);");
			
			$plugin_id = $db->lastInsertId();
								
			$this->_last_insert_id = $plugin_id;
			
			call_user_func_array($install_hook, array($plugin_id));
			
			//If more than one plugin needs to install itself, don't reuse the same form submission
			unset($_POST);
			
		} catch (Exception $e) {
			
			echo "An error occurred when installing this plugin: ".$e->getMessage();
			
			//If there was an error, remove the plugin from the DB so that we can retry the install
			if(isset($plugin_id) and is_int($plugin_id)) {
				get_db()->exec("DELETE FROM plugins WHERE id = $plugin_id");
			}
		}

	}
	
	public function defineMetafield($name, $description) 
	{
		$plugin = $this->getCurrentPlugin();
		
		//Get the ID of the last inserted plugin
		$id = $this->_last_insert_id;
		
		$db = get_db();
		
		$db->exec("INSERT IGNORE INTO $db->Metafield (name, description, plugin_id) VALUES (?, ?, ?)", array($name, $description, $id));
	}
	
	/**
	 * used by the add_theme_pages() helper to create a list of directories that can store static pages that integrate into the themes
	 *
	 * @return void
	 **/
	public function addThemeDir($path, $theme)
	{
		if(!in_array($theme, array('public','admin','both')))
			return false;
		
		//Path must begin from within the plugin's directory
		
		$path = $this->getCurrentPlugin() . DIRECTORY_SEPARATOR . $path;
		
		switch ($theme) {
			case 'public':
				$this->_theme_dirs['public'][] = $path;
				break;
			
			case 'admin':
				$this->_theme_dirs['admin'][] = $path;
				break;
			
			case 'both':
				$this->_theme_dirs['public'][] = $path;
				$this->_theme_dirs['admin'][] = $path;
				break;
			
			default:
				# code...
				break;
		}
		
			
		$this->_theme_dirs[$theme][] = $path;
	}
	
	/**
	 * This will hook into the Zend_View API to add whatever is stored in self::$_theme_dirs to the script path
	 *
	 * 
	 * @return void
	 **/
	public function loadThemeDirs(Omeka_View $view, $theme)
	{
		$this->registerScriptPaths($view, $this->_theme_dirs[$theme]);
	}
		
	/**
	 * @see Omeka_View::setThemePath()
	 *
	 * @return void
	 **/	
	protected function registerScriptPaths($view, $paths)
	{
		//Grab the web paths of the other plugins
		if(Zend_Registry::isRegistered('plugin_web_paths')) {
			$webPaths = Zend_Registry::get( 'plugin_web_paths' );
		}else {
			$webPaths = array();
		}

		foreach ($paths as $path) {
			$physicalPath = PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
			$view->addScriptPath($physicalPath);
			
			$webPaths[$physicalPath] = WEB_PLUGIN . DIRECTORY_SEPARATOR . $path;
		}
		
		Zend_Registry::set('plugin_view_paths', $webPaths);		
	}
	
	/**
	 * This will make an entire directory of controllers available to the front controller
	 * If no directory is provided then the current plugin directory is used
	 * People who don't know MVC probably won't use this much
	 * 
	 * @return void
	 **/
	public function addControllerDir($path=null, $module=null)
	{
		$front = Zend_Controller_Front::getInstance();
		
		$current = $this->getCurrentPlugin();
		
		$dir = PLUGIN_DIR . DIRECTORY_SEPARATOR . $current . ($path ? DIRECTORY_SEPARATOR . $path : ''); 

		if(!$module) {
			$module = strtolower($current);
		}
	
		$front->addControllerDirectory($dir, $module);
		
	}
	
	/**
	 * @since 11/26/07
	 *
	 * @return void
	 **/
	public function addFeed($format, $options)
	{
		$this->_feeds[$format][] = $options;
	}
	
	public function hasFeed($format)
	{
		return (bool) (count($this->_feeds[$format]));
	}
	
	public function getFeed($format)
	{
		return $this->_feeds[$format];
	}
	
	/**
	 * @since 10/2/07 current navigation types include: 'main', 'archive', 'settings', 'users'
	 *
	 * @return void
	 **/
	public function addNavigation($text, $link, $type='main', $permissions)
	{
		$nav = $this->_nav;
		
		$new = array('text'=>$text,'link'=>$link, 'permissions'=>$permissions);

		$nav[$type][] = $new;
		$this->_nav = $nav;
	}
	
	/**
	 * This gets fired from within the admin theme to load plugin-defined navigation elements
	 *
	 * @see nav() theme helper function
	 * @return void
	 **/
	public function load_navigation($type)
	{
		if(!isset($this->_nav[$type])) return;
		
		foreach ($this->_nav[$type] as $nav) {
			
			$link = $nav['link'];
			$text = $nav['text'];
			$permissions = $nav['permissions'];
			
			//Make a quick permissions check
			if($permissions) {
				$resource = $permissions[0];
				$rule = $permissions[1];
				
				if(!has_permission($resource, $rule)) {
					continue;
				}
			}
			
			//Actually create the link (test if it is local or not)
			//If it has an 'http' in it, its not local, otherwise it is
			if(!preg_match('/^http/', $link)) {
				$link = uri($link);
			}		
			echo '<li class="' . text_to_id($text, 'nav') . (is_current($link) ? ' current':''). '"><a href="' . $link . '">' . h($text) . '</a></li>';
		}
	}
	
	/**
	 * This handles dispatching all plugin hooks
	 *
	 * @return array|void
	 **/
	public function __call($hook, $a) {
//var_dump( $hook );

		if(empty($this->_callbacks[$hook]))	return;

		foreach( $this->_callbacks[$hook] as $plugin=>$callback )
		{
			if($this->isActive($plugin)) {
				
				//Make sure the callback executes within the scope of the current plugin
				$this->setCurrentPlugin($plugin);
				
				call_user_func_array($callback, $a);
			}
		}
	}
}

function add_plugin_hook($hook, $callback)
{
	$broker = Zend_Registry::get( 'plugin_broker' );
	$broker->addHook($hook, $callback);
} 

/**
 * fire_plugin_hook('save_item', $item, $arg2)  would call the plugin hook 'save_item' with those 2 arguments
 *
 * @return void
 **/
function fire_plugin_hook()
{
	$args = func_get_args();
	
	$hook = array_shift($args);
		
	call_user_func_array(array(get_plugin_broker(), $hook), $args);
}

function get_plugin_broker()
{
	return Zend_Registry::get( 'plugin_broker' );
}

function define_metafield($name, $description, $type=null)
{
	$broker = Zend_Registry::get( 'plugin_broker' );
	$broker->defineMetafield($name, $description, $type);
}

function add_theme_pages($dir, $theme='both')
{
	get_plugin_broker()->addThemeDir($dir, $theme);
}

function add_controllers($dir='controllers')
{
	get_plugin_broker()->addControllerDir($dir);
}

function add_data_feed($format, $options=array())
{
	get_plugin_broker()->addFeed($format, $options);
}

function add_navigation($text, $link, $type='main', $permissions=null)
{
	get_plugin_broker()->addNavigation($text, $link, $type, $permissions);
}

function get_acl()
{
	return Zend_Registry::get( 'acl' );
}
?>

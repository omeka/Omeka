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
	
	//Output directories that have been added by plugins
	protected $_output_dirs = array('json'=>array(), 'rest'=>array());

	//Routes that have been added by plugins
	protected $_routes = array();
	
	public function __construct() 
	{
		Zend::register('plugin_broker', $this);
		
		//Construct the current list of potential, installed & active plugins
		require_once 'VersionedDirectoryIterator.php';
		
		//Loop through all the plugins in the plugin directory
		$dir = new VersionedDirectoryIterator(PLUGIN_DIR);
		$names = $dir->getValid();
		
		$this->_all = $names;
		
		foreach ($names as $name) {
			$this->setCurrentPlugin($name);
	
			//Require the file that contains the plugin
			$path = PLUGIN_DIR . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'plugin.php';
			if(file_exists($path)) {
				require_once $path;
			}
		}
		
		//Get the list of currently installed plugins
		if(empty($this->_installed)) {
			$installed = array();
			$active = array();
			
			$res = db_query('SELECT p.name, p.active FROM plugins p');
			foreach ($res as $row) {
				$installed[$row['name']] = $row['name'];
				
				if($row['active']) {
					$active[$row['name']] = $row['name'];
				}
			}
			$this->_active = $active;
			$this->_installed = $installed;
		}
		
		//Fire all the 'initialize' hooks for the plugins
		$this->initialize();
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
	 * Special installer hook (checks DB to see if plugin is already there)
	 * If the plugin is not there, it will check for 'install_form' hooks
	 *
	 * @return void
	 **/
	public function install() {
		
		//Loop through all the possible plugins
		foreach ($this->_all as $plugin) {

			//Make sure the plugin helpers like define_metafield() etc. that need to be aware of scope can operate on this plugin
			$this->setCurrentPlugin($plugin);
			
			//If the current plugin is not installed
			if(!$this->isInstalled($plugin)) {
				
				$form_hook = $this->_callbacks['install_form'][$plugin];
				
				//If we haven't POSTed anything, Check to see whether the plugin has an install form
				if( empty($_POST) and !empty($form_hook) ) {
					require_once HELPERS;
					head();
					//This can be wrapped a bit better in the future
					echo '<form action="" method="post">';
					//Load whatever form the plugin writer has provided and exit
					call_user_func_array($form_hook, array());
					echo '<input type="submit" name="install_plugin" />';
					echo '</form>';
					foot();
					exit;			
				}
			
				//Now run the installer for the plugin
				$install_hook = $this->_callbacks['install'][$plugin];

				try {
					//Insert the plugin into the DB
					db_query("INSERT IGNORE INTO plugins (name, active) VALUES ('$plugin', 1);");
					
					$res = db_query("SELECT LAST_INSERT_ID() as new_id");
					
					$plugin_id = $res[0]['new_id'];
					
					$this->_last_insert_id = $plugin_id;
					
					call_user_func_array($install_hook, array($plugin_id));
					
					//If more than one plugin needs to install itself, don't reuse the same form submission
					unset($_POST);
					
				} catch (Exception $e) {
					
					//If there was an error, remove the plugin from the DB so that we can retry the install
					if(isset($plugin_id) and is_int($plugin_id)) {
						db_query("DELETE FROM plugins WHERE id = $plugin_id");
					}
				}
			}
		}

	}
	
	public function defineMetafield($name, $description) 
	{
		$plugin = $this->getCurrentPlugin();
		
		//Get the ID of the last inserted plugin
		$id = $this->_last_insert_id;
		
		db_query("INSERT IGNORE INTO metafields (name, description, plugin_id) VALUES (?, ?, ?)", array($name, $description, $id));
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
	 * Make a directory containing files that output in a specific format (current options = 'rest' and 'json')
	 *
	 * @return void
	 **/
	public function addOutputDir($path, $type)
	{
		if(!in_array($type, array('json', 'rest'))) return;
		
		$this->_output_dirs[$type][] = $this->getCurrentPlugin() . DIRECTORY_SEPARATOR . $path;
		
	}
	
	public function addRoute()
	{
		$args = func_get_args();
				
		$this->_routes[] = $args;	
	}
	
	public function loadRoutes($router)
	{	
		
		foreach ($this->_routes as $route) {
			call_user_func_array(array($router, 'addRoute'), $route);	
		}
	}
	
	public function loadOutputDirs(Omeka_View $view, $type)
	{
		$this->registerScriptPaths($view, $this->_output_dirs[$type]);
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
		if(Zend::isRegistered('plugin_web_paths')) {
			$webPaths = Zend::Registry( 'plugin_web_paths' );
		}else {
			$webPaths = array();
		}

		foreach ($paths as $path) {
			$physicalPath = PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
			$view->addScriptPath($physicalPath);
			
			$webPaths[$physicalPath] = WEB_PLUGIN . DIRECTORY_SEPARATOR . $path;
		}
		
		Zend::register('plugin_view_paths', $webPaths);		
	}
	
	/**
	 * This will make an entire directory of controllers available to the front controller
	 * If no directory is provided then the current plugin directory is used
	 * People who don't know MVC probably won't use this much
	 * 
	 * @return void
	 **/
	public function addControllerDir($path=null)
	{
		$front = Omeka_Controller_Front::getInstance();
		
		$current = $this->getCurrentPlugin();
		
		$dir = PLUGIN_DIR . DIRECTORY_SEPARATOR . $current . ($path ? DIRECTORY_SEPARATOR . $path : ''); 
		
		$front->addControllerDirectory($dir);
		
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
	$broker = Zend::Registry( 'plugin_broker' );
	$broker->addHook($hook, $callback);
} 

function db_query($sql, $params=array())
{
	try {
		$conn = Doctrine_Manager::getInstance()->connection();
		return $conn->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
	} catch (Exception $e) {
		echo $e->getMessage();exit;
	}
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
	return Zend::Registry( 'plugin_broker' );
}

function define_metafield($name, $description, $type=null)
{
	$broker = Zend::Registry( 'plugin_broker' );
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

/**
 * Facade for the Zend Router interface
 *
 * for example: add_route(
 *   'user',
 *   new Zend_Controller_Router_Route('user/:username', array('controller' => 'user', 'action' => 'info'))
 * @return void
 **/
function add_route()
{
	$name = func_get_arg(0);
	$def = func_get_arg(1);
	get_plugin_broker()->addRoute($name, $def);
}

function add_output_pages($dir, $output_type='rest')
{
	get_plugin_broker()->addOutputDir($dir, $output_type);
}

function set_option($name, $value)
{
	db_query("REPLACE INTO options (name, value) VALUES (?,?)", array($name, $value));
}

function get_acl()
{
	return Zend::Registry( 'acl' );
}

?>

<?php
require_once 'Zend/Controller/Plugin/Abstract.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Plugin.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'PluginTable.php';
require_once 'Zend/Config/Ini.php';
/**
 * Specialized plugin class
 *
 * @package Omeka
 * 
 **/
abstract class Kea_Plugin extends Zend_Controller_Plugin_Abstract
{
	private $record;
	private $listener;
	public $router;
	
	/**
	 * Path to the plugin directory
	 *
	 * @var string
	 **/
	protected $dir;
		
	public function __construct($router, $record) {
		$this->dir = PLUGIN_DIR.DIRECTORY_SEPARATOR.get_class($this);
		
		$iniFile = $this->dir.DIRECTORY_SEPARATOR.'plugin.ini';
		
		if(!file_exists($iniFile)) 
		{
			throw new Exception( get_class($this).' plugin is missing the plugin.ini file.' );
		}
		$ini = new Zend_Config_Ini($iniFile);
		$this->ini = $ini;
		
		if(!$record) {
			throw new Exception( 'Plugin must have an associated record in the database' );
		}
		
		$this->record = $record;
		
		if($record->active) {
			if(isset($ini->routes)) {
				$router->addConfig($ini, 'routes');			
			}

			$this->router = $router;			

			//Hook the Doctrine event listeners into the plugin
			$listener = new Kea_EventListener($this);
			Doctrine_Manager::getInstance()->getListener()->add($listener);
	
			$front = Kea_Controller_Front::getInstance();
			if(file_exists($this->dir.DIRECTORY_SEPARATOR.'controllers')) {
				$front->addControllerDirectory($this->dir.DIRECTORY_SEPARATOR.'controllers');
			}
		
			Zend::register(get_class($this), $this);
			$this->bindModelRelations();
			
			$this->init();
		}
		
	}
	
	public function bindModelRelations()
	{
		if(isset($this->ini->relations)) {
			foreach ($this->ini->relations as $key => $entry) {
				Kea_Controller_Plugin_Broker::getInstance()->addBound($entry->Class, array($entry->Type, $entry->Component, $entry->Link));
			}			
		}
	}
	
	public function init() {}
	
	///// INSTALLATION/ACTIVATION /////
	
	/**
	 * Install this plugin given its path
	 *
	 * @todo Have it check for specific types in the metafield definition so that the plugin can add metafields only to certain types
	 * @return void
	 **/
	public function install() {
		if(!$this->record->exists()) {
			if($defaults = $this->ini->config) 
			{
				$defaults = $defaults->asArray();
				$config = array();
				foreach( $defaults as $key => $default )
				{
					$config[$default['name']] = $default['value'];
				}
				$this->record->config = $config;
			}
			else 
			{
				$this->record->config = array();			
			}
			
			$this->record->name = get_class($this);
			$this->record->description = $this->ini->info->description;
			$this->record->author = $this->ini->info->author;
			
			if($metafields = $this->ini->metafields) 
			{
				$metafields = $metafields->asArray();
				foreach( $this->ini->metafields->asArray() as $array )
				{
					$metafield = new Metafield;
					foreach( $array as $key => $value )
					{
						$metafield->$key = $value;
					}
					$metafield->save();
					$this->record->Metafields->add($metafield);
				}				
			}

			$this->record->save();	
			$this->customInstall();
		}else {
			throw new Exception(get_class($this).' plugin has already been installed.');
		}
	}
	
	/**
	 * Convenience method for plugin writers to customize their plugin installation
	 *
	 * @return void
	 * 
	 **/
	public function customInstall() {}
	
	public function activate() {
		$this->record->active = 1;
		$this->record->save();
	}
	
	public function deactivate() {
		$this->record->active = 0;
		$this->record->save();
	}
	
	///// CONVENIENCE METHODS /////
	
	public function uri($urlEnd) {
		return rtrim($this->getRequest()->getBaseUrl(), '/').'/'.$urlEnd;
	}
	
	///// RECORD GETTER/SETTER /////
	
	public function getConfig($index) {
		return $this->record->config[$index];
	}
	
	public function setConfig($index, $val) {
		$this->record->config[$index] = $val;
	}
	
	public function metafields() {
		return $this->record->Metafields;
	}
	
	public function webPath() {
		return WEB_PLUGIN.DIRECTORY_SEPARATOR.get_class($this);
	}
	
	public function __get($name) {
		return $this->record->$name;
	}
	
	///// CUSTOM OMEKA HOOKS /////
	
	/**
	 * Echo all javascript includes, css files, etc. here so that they will be properly included in the template header
	 *
	 * @return void
	 **/
	public function header() {
		require_once 'Kea/View/Functions.php';
		
		$path = $this->dir.DIRECTORY_SEPARATOR.'header.php';
		if(file_exists($path)) {
			include $path; 
		}
	}
	
	/**
	 * Ditto for the footer (not sure if this will be terribly useful)
	 *
	 * @return void
	 **/
	public function footer() {}
	
	/**
	 * Add navigation to themes at any given point in a view that a theme writer uses nav()
	 *
	 * Right now it only positions new navigation after existing navigation, but the plugin writer
	 * can choose which navigation goes after other built-in navigation
	 *
	 * @param string Can place new navigation elements after elements with this link text
	 * @param string New navigation can go after elements with this link uri
	 * @usage If $text is 'Themes', then the return value will add itself to any nav() that contains 'Themes', but only right after 'Themes'
	 * @return array Key = Text of link, Value = uri
	 **/
	public function addNavigation($text, $link, $position = 'after') {}	
	
	public function addScriptPath($view, $type = null) {
		switch ( $type )
		{
			case 'json':
			case 'rest':
				$view->addScriptPath($this->dir.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$type);
			break;
			default:
				$view->addScriptPath($this->dir.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'theme');
			break;
		}
		
	}
	
	///// ZEND CONTROLLER HOOKS /////
	
	/**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {}

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after Zend_Controller_Front exits from the router.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {}

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {}

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {}

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {}

    /**
     * Called before Zend_Controller_Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {}
	
	///// END ZEND CONTROLLER HOOKS /////
	
	///// DOCTRINE LISTENERS /////
	
	protected function dispatchListener($type, $record)
	{
		$method = $type.get_class($record);
		if(method_exists($this, $method)) {
			call_user_func_array(array($this, $method), array($record));
		}
	}
		
	public function onLoad(Doctrine_Record $record) {
		$this->dispatchListener('onLoad', $record);
	}
    public function onPreLoad(Doctrine_Record $record) {
		$this->dispatchListener('onPreLoad', $record);
	}
    public function onUpdate(Doctrine_Record $record) {}
    public function onPreUpdate(Doctrine_Record $record) {}

    public function onCreate(Doctrine_Record $record) {}
    public function onPreCreate(Doctrine_Record $record) {}
 
    public function onSave(Doctrine_Record $record) {
		$this->dispatchListener('onSave', $record);
	}
    public function onPreSave(Doctrine_Record $record) {}
 
    public function onInsert(Doctrine_Record $record) {
		$this->dispatchListener('onInsert', $record);
	}
    public function onPreInsert(Doctrine_Record $record) {}
 
    public function onDelete(Doctrine_Record $record) {}
    public function onPreDelete(Doctrine_Record $record) {}
 
    public function onEvict(Doctrine_Record $record) {}
    public function onPreEvict(Doctrine_Record $record) {}
 
    public function onSleep(Doctrine_Record $record) {}
    
    public function onWakeUp(Doctrine_Record $record) {}
    
    public function onClose(Doctrine_Connection $connection) {}
    public function onPreClose(Doctrine_Connection $connection) {}
    
    public function onOpen(Doctrine_Connection $connection) {}
 
    public function onTransactionCommit(Doctrine_Connection $connection) {}
    public function onPreTransactionCommit(Doctrine_Connection $connection) {}
 
    public function onTransactionRollback(Doctrine_Connection $connection) {}
    public function onPreTransactionRollback(Doctrine_Connection $connection) {}
 
    public function onTransactionBegin(Doctrine_Connection $connection) {}
    public function onPreTransactionBegin(Doctrine_Connection $connection) {}
    
    public function onCollectionDelete(Doctrine_Collection $collection) {}
    public function onPreCollectionDelete(Doctrine_Collection $collection) {}
	
	public function filterBrowse(Kea_Controller_Browse_Abstract $browseObj) {}
	
	public function addToTitle() {}
} // END class Kea_Plugin extends Zend_Controller_Plugin_Abstract

?>
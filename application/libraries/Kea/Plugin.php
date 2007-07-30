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
	
	protected $config = array();
	protected $metafields = array();
	protected $types = array();
	protected $typesMetafields = array();
	protected $metaInfo = array();
	
	protected $scriptPath = array(	'theme'	=> array('views/theme'),
									'json'	=> array('views/json'),
									'rest'	=> array('views/rest'));
	
	/**
	 * Path to the plugin directory
	 *
	 * @var string
	 **/
	protected $dir;
		
	public function __construct($router, $record) {
		$this->dir = PLUGIN_DIR.DIRECTORY_SEPARATOR.get_class($this);
		
		if(!$record) {
			throw new Exception( 'Plugin must have an associated record in the database' );
		}
		
		$this->record = $record;
		
		$this->definition();
		
		if($record->active) {
			$routesFile = $this->dir.DIRECTORY_SEPARATOR.'routes.ini';
			
			if(file_exists($routesFile)) {
				$ini = new Zend_Config_Ini($routesFile);
			
				if(isset($ini->routes)) {
					$router->addConfig($ini, 'routes');			
				}			
			}

			$this->router = $router;			
	
			$front = Kea_Controller_Front::getInstance();
			if(file_exists($this->dir.DIRECTORY_SEPARATOR.'controllers')) {
				$front->addControllerDirectory($this->dir.DIRECTORY_SEPARATOR.'controllers');
			}
		
			Zend::register(get_class($this), $this);
			
			$this->onStartup();
		}
		
	}
/*
		
	public function bindModelRelations()
	{
		if(isset($this->ini->relations)) {
			foreach ($this->ini->relations as $key => $entry) {
				Kea_Controller_Plugin_Broker::getInstance()->addBound($entry->Class, array($entry->Type, $entry->Component, $entry->Link));
			}			
		}
	}
*/	
	
	public function onStartup() {}
	
	///// INSTALLATION/ACTIVATION /////
	
	/**
	 * Install this plugin given its path
	 *
	 * @todo Have it check for specific types in the metafield definition so that the plugin can add metafields only to certain types
	 * @return void
	 **/
	public function install($config=array()) {
				
		if(!$this->record->exists()) {
			$this->record->config = $config;
			
			$this->record->name = get_class($this);
			$this->record->active = 1;
			$this->record->save();	
			
			$plugin_id = $this->record->id;
			
			if(!$plugin_id) {
				throw new Exception( 'Plugin install failed.' );
			}
			
			$mfTable = Zend::Registry('doctrine')->getTable('Metafield');
			$typesTable = Zend::Registry('doctrine')->getTable('Type');
			
			//Add metafields to existing types			
			$dql = "SELECT tm.* FROM TypesMetafields tm 
					INNER JOIN tm.Type t 
					INNER JOIN tm.Metafield m WHERE t.name = ? AND m.name = ?";
			$q = new Doctrine_Query;
			$q->parseQuery($dql);
			
			foreach ($this->typesMetafields as $typeName => $mfDef) {
				$tm = $q->execute(array($typeName,$mfDef['name']))->getFirst();
				$type = $typesTable->findByName($typeName);
				$mf = $mfTable->findByName($mfDef['name']);
				if(!$tm) {
					$tm = new TypesMetafields;
					$tm->Metafield->setArray($mfDef);
					$tm->Type = $type;
					$tm->plugin_id = $plugin_id;
					$mf->save();
				}
			}
			
			//Add the metafields
			foreach ($this->metafields as $name => $def) {
				$mf = $mfTable->findByName($name);
				if(!count($mf)) {
					$mf = new Metafield;
					$mf->setArray($def);
					$mf->plugin_id = $plugin_id;
					$mf->save();
				}
				
			}
			
			//Add the types
			foreach ($this->types as $name => $def) {
				$t = $typesTable->findByName($name);
				if(!$t) {
					$t = new Type;
					$t->name = $def['name'];
					$t->description = $def['description'];
					foreach ($def['metafields'] as $key => $mf) {
						$t->Metafields[$key]->setArray($mf);
					}
					$t->plugin_id = $plugin_id;
					$t->save();
				}
			}
			
			$this->customInstall();
		}else {
			throw new Exception(get_class($this).' plugin has already been installed.');
		}
	}
	
	public function definition() {}
	
	public function hasConfig($name, $description=null, $default=null) {
		$this->config[$name] = array('description'=>$description, 'default'=>$default);
	}

	public function getConfigDefinition() {
		return $this->config;
	}
	
	public function hasMetaInfo($props = array()) {
		$this->metaInfo = $props;
	}
	
	public function getMetaInfo($name=null) {
		return ($name) ? $this->metaInfo[$name] : $this->metaInfo;
	}
	
	public function hasMetafield($name, $description=null) {
		$this->metafields[$name] = array('name'=>$name, 'description'=>$description);
	}
	
	public function hasType($name, $description=null, $metafields = array()) {
		$this->types[$name] = array('name'=>$name, 'description'=>$description, 'metafields'=>$metafields);
	}
	
	public function typeHasMetafield($typeName, $metafieldName, $metafieldDescription=null) {
		$this->typesMetafields[$typeName] = array('name'=>$metafieldName, 'description'=>$metafieldDescription);
	}
	
	public function hasScriptPath($path, $type='theme') {
		$this->scriptPath[$type][] = $path;
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
		require_once 'Kea/View/Functions.php';
		return uri($urlEnd);
	}
	
	///// RECORD GETTER/SETTER /////
	
	public function getDbConn()
	{
		return Doctrine_Manager::getInstance()->connection();
	}
	
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
/*
		
	public function __get($name) {
		return $this->record->$name;
	}
*/	
	
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
	
	public function addScriptPath($view, $type = 'theme') {
		//Grab the web paths of the other plugins
		if(Zend::isRegistered('plugin_web_paths')) {
			$webPaths = Zend::Registry( 'plugin_web_paths' );
		}else {
			$webPaths = array();
		}

		foreach ($this->scriptPath[$type] as $path) {
			$physicalPath = $this->dir.DIRECTORY_SEPARATOR.$path;
			$view->addScriptPath($physicalPath);
			
			$webPaths[$physicalPath] = WEB_PLUGIN . DIRECTORY_SEPARATOR . get_class($this) . DIRECTORY_SEPARATOR . $path;
		}
		
		Zend::register('plugin_view_paths', $webPaths);
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
	
	public function preRenderPage($page, $vars) {}
	public function postRenderPage($page, $vars) {}
	
	public function onBrowseItems($items) {} 
	public function onShowItem($item) {} 
	public function onDeleteItem($item) {}
	public function onAddItem($item) {} 
	public function onEditItem($item) {}
	public function onTagItem($item, $tagName, $user_id) {}
	public function onUntagItem($item, $tagName, $user_id) {} 
	public function onMakePublicItem($item) {}
	public function onMakeFavoriteItem($item, $user_id) {}
	
	public function onAddCollection($coll) {}
	public function onEditCollection($coll) {}
	public function onBrowseCollections($colls) {}
	public function onShowCollection($coll) {}
	public function onDeleteCollection($coll) {}
	
	public function onBrowseExhibits($exhibits) {}
	public function onAddExhibit($ex) {}
	public function onEditExhibit($ex) {}
	public function onDeleteExhibit($ex) {}
	public function onTagExhibit($ex, $tags) {}
	public function onUntagExhibit($ex, $tags) {}
	public function onShowExhibit($ex, $section, $page) {}
	public function onAddExhibitPage($page) {}
	public function onAddExhibitSection($section) {}
	public function onEditExhibitPage($page) {}
	public function onEditExhibitSection($section) {}
	public function onDeleteExhibitPage($page) {}
	public function onDeleteExhibitSection($section) {}
	public function onShowExhibitItem($item, $exhibit) {}
	
	public function onAddType($type) {}
	public function onEditType($type) {}
	public function onDeleteType($type) {}
	public function onShowType($type) {}
	public function onBrowseTypes($types) {}		
	
	public function onBrowseTags($tags, $for) {}
	
	//Low-level DB Hooks (why did I get rid of these in the first place?)
	public function onDeleteRecord($record) {}
	
} // END class Kea_Plugin extends Zend_Controller_Plugin_Abstract

?>
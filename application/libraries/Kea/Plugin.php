<?php
/*TODO: collapse this and Plugin model (all the logic can be in one place, shouldn't be all spread out)
/**
 * Kea_Plugin
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
abstract class Kea_Plugin extends Plugin implements Kea_Plugin_Interface
{
	private $tempStorage = array();
	private $manager;
	
	/**
	 * array of metafields for each plugin with metafields[]['name'] & metafields[]['description']
	 *
	 * @var array
	 **/
	protected $metafields = array();
	
	public function __construct($table = null, $isNewEntry = false) {
		parent::__construct($table, $isNewEntry);
	}
	
	/**
	 * This is how the Plugin Manager tells the individual plugins to do their thing
	 *
	 * @param string the notification string, typically corresponding to a method name within the plugin
	 * @param object the object from whence the notification sprang, or any arbitrary object (optional)
	 * @param array  other parameters to pass to the plugin method (optional)
	 * @return mixed the result of running the plugin method, or false if there is no given method
	 * @author Kris Kelly
	 **/
	public function update($msg, &$obj, $params) {
		if( method_exists($this, $msg) ) {
			return call_user_func_array(array($this, $msg), array($obj, $params));
		}
		return false;
	}
	
	public function install(array $config) {
		if(!$this->isInstalled())
		{
			$this->name = get_class($this);
			//Maybe this should be passed as opposed to hard-coded, mais je ne sais pas
			$this->path = PLUGIN_PATH.$this->name.EXT;
			$this->config = $config;
			
			foreach( $this->metafields as $k => $v )
			{
				$this->Metafields[$k]->setArray($v);
			}
			
			$this->activate();
		}
	}
	
	public function isInstalled() {
		$res = $this->getTable()->findBySql("name = '".get_class($this)."'" );
		return ($res->count() > 0);
	}
	
	/**
	 * Get configuration elements, which are stored as a serialized array in the DB
	 *
	 * @return mixed
	 * @author Kris Kelly
	 **/
	public function config($index) {
		return $this->config[$index];
	}
	
	public function setConfig($index, $value) {
		$this->config[$index] = $value;
		$this->save();
	}
	
	public function activate() {
		$this->active = 1;
		$this->save();
	}
	
	public function deactivate() {
		$this->active = 0;
		$this->save();
	}
	
	public function view($view, $vars, $return) {
		$CI =& get_instance();
		$path = dirname($this->path).'/views/'.$view.EXT;
		if(file_exists($path)) {
			return $CI->load->_ci_load(array('view' => $view, 'vars' => $CI->load->_ci_object_to_array($vars), 'path' => $path, 'return' => $return));
		}
		elseif( $this->manager()->debug() ) {
//			echo $path . ' view does not exist';
		}
	}
	
	public function store($index, $value) {
		$this->tempStorage[$index] = $value;
	}
	
	public function retrieve($index) {
		return (!empty($this->tempStorage[$index])) ? $this->tempStorage[$index] : false;
	}
		
	public function manager(&$obj = null) {
		if($obj === null) {
			return $this->manager;
		} else {
			$this->manager = $obj;
		}
	}
	
} // END class Kea_Plugin

?>
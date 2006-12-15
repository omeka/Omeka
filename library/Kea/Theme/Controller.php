<?php
/**
 * @created 10/13/06    
 * @edited 10/13/06
 */
require_once 'Kea/Theme/Exception.php';
class Bar {
	public $title = "goo";
	public $fizzle = array('snoop', 'dog');

}
class Kea_Theme_Controller
{
	private $_theme = KEA_THEME;
	private $_themes_dir = KEA_THEME_PATH;
	private $_output = array();
	
	public function __set($name, $val)
	{
		$this->_output[$name] = $val;
	}
	
	public function __get($name)
	{
		if (isset($this->_output[$name])) {
			return $this->_output[$name];
		}
		return false;
	}
	
	public function render($controller, $action)
	{
		// These should be relatively safe-formated already and lowercased.
		$request = Kea_Request::getInstance();
		if ($request->get('format') && $request->get('format') == 'json') {
			require_once 'Zend/Json.php';
			return Zend_Json::encode($this->_output);
		}
		
		$theme_dir = $this->_themes_dir . DIRECTORY_SEPARATOR . $this->_theme . DIRECTORY_SEPARATOR . $controller;
		$file = $action;
		
		if ($fullpath = Kea::loadFile($theme_dir, $file, false)) {
			ob_start();
			include $fullpath;
			return ob_get_clean();
		}
	}
	
	public function getHeader($file="header")
	{
		$header = $this->render('shared', $file);
		/* Add some plugin functionality here to mod the header
		 */
		echo $header;
	}
	
	public function getFooter($file="footer")
	{
		$footer = $this->render('shared', $file);
		/*
		 * Add plugin functionality to mod the footer
		 */
		echo $footer;
	}
}

?>
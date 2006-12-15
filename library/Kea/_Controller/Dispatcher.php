<?php
/**
 * @edited 10/13/06 n8agrin
 */
require_once 'Kea/Controller/Dispatcher/Exception.php';
class Kea_Controller_Dispatcher
{
	private $_controller_paths;

	public function __construct()
	{
		$this->_controller_paths = (array) KEA_CONTROLLER_PATH;
		/*
		if (is_array($controller_path)) {
			$this->_controller_paths = $controller_path;
		}
		else {
			$this->_controller_paths = array(trim($controller_path, '/'));	
		}
		*/
	}

	public function route(Kea_Request $request)
	{
		$next = $request->nextAction();
		
		$action = $next['action'];
		$controller = $next['controller'];
		$cName = ucfirst($controller) . 'Controller';
		
		foreach ($this->_controller_paths as $path) {
			$file = $path . DIRECTORY_SEPARATOR . $cName . '.php';
			
			if (file_exists($file)) {
				break;
			}
			else {
				$file = null;
			}
		}
		
		if ($file === null) {
			throw new Kea_Router_Exception(
				'There is no controller file named "' . $controller . '"');
		}
		else {
			require_once $file;
			$reflect = new ReflectionClass($cName);
			if ($reflect->hasMethod($action) &&
				$reflect->getMethod($action)->isPublic()) {
					$controller = new $cName;
					$controller->beforeFilter($action, $controller);
					$controller->{$action}();
					//$result = $controller->afterFilter($result);
					//return $result;
			}
			else {
				throw new Kea_Router_Exception(
					'There is no action for the controller ' . $cName . ' called ' . $action);
			}
		}
	}
}

?>
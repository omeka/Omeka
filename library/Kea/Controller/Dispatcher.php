<?php
/**
 * @edited 10/13/06 n8agrin
 */
require_once 'Kea/Controller/Dispatcher/Exception.php';
require_once 'Kea/Controller/Response/Abstract.php';
class Kea_Controller_Dispatcher
{
	private $_controller_paths;

	public function __construct()
	{
		$this->_controller_paths = (array) KEA_CONTROLLER_DIR;
	}

	public function route(Kea_Request $request, Kea_Controller_Response_Abstract $response)
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
			$paction = '_'.$action;
			if ($reflect->hasMethod($paction) &&
				$reflect->getMethod($paction)->isProtected()) {
					$controller = new $cName($response);
					$controller->{$action}();
			}
			else {
				throw new Kea_Router_Exception(
					'There is no action for the controller ' . $cName . ' called ' . $action);
			}
		}
	}
}

?>
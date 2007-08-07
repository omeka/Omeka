<?php
require_once 'Zend/Controller/Front.php';
require_once 'Kea/Controller/Plugin/Broker.php';
/**
 * customized Zend Front Controller
 *
 * @package Sitebuilder
 * 
 **/
class Kea_Controller_Front extends Zend_Controller_Front
{
	
	/**
     * Singleton instance
     * @var self 
     */
    private static $_instance = null;
	
	private function __construct()
    {
        $this->_plugins = Kea_Controller_Plugin_Broker::getInstance();
    }

	public function getPlugins() 
	{
		return $this->_plugins->plugins();
	}
	
	/**
     * Singleton instance
     * 
     * @return Kea_Controller_Front
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	public function dispatch($debugExceptions = false)
	{
		try {
			return parent::dispatch();
		} 
		catch (Exception $e) {
			try {
				if($this->isMissingController($e)) {
					$rendered = $this->renderStaticPage();
				}
				
			} catch (Exception $e) {
				$this->render404($e, $debugExceptions);
			}
			
			if(!$rendered) {
				$this->render404($e, $debugExceptions);
			}		
		}
		
	}
	
	/**
	 * Hack that determines whether the error is a dispatch error
	 * @change for ZF 1.0.0
	 * @return void
	 **/
	protected function isMissingController($e)
	{
		if($e instanceof Zend_Exception) {
			$msg = $e->getMessage();
			
			if(substr($msg, 0, 6) == 'File "') {
				return true;
			}
		}
		return false;
	}

	protected function renderStaticPage()
	{
		$req = $this->getRequest();
		$c = $req->getControllerName();
		$a = $req->getActionName();
		
		//'index' action corresponds to a uri like foobar/
		if($a == 'index') {
			$page = $c;
			$dir = null;
		}
		//Any combo of controller/action corresponds to a page like foobar/thing.php
		else {
			$page = $a;
			$dir = $c;
		}
		
		if(!$dir) {
			$file = $page . '.php';
		}else {
			$file = $dir . DIRECTORY_SEPARATOR . $page . '.php';
		}
		
		$view = new Kea_View(null, array('request'=>$req));
		echo $view->render($file);
		
		return true;
	}
	
	protected function render404($e, $debugExceptions = false)
	{
		Kea_Logger::logError( $e );
		if($debugExceptions) {
			include BASE_DIR . DIRECTORY_SEPARATOR .'404.php';
			exit;	
		}else {
			$front = Kea_Controller_Front::getInstance();
			$view = new Kea_View(null, array('request'=>$front->getRequest()));
			echo $view->render('404.php');
			exit;		
		}	
	}

} // END class Kea_Controller_Front extends Zend_Controller_Front

?>
<?php 
/**
* Modelled after sfContext in Symfony framework
*
* Kind of a bootstrap class
*
* Stores all (essentially global) data that is needed by the application
*
* Examples of this include, but are not limited to: database connection, config file data, ACL, Auth, logger, etc.
*/
class Omeka_Context
{
	private static $_instance;
    
    protected 
        $_db,
        $_config = array(),
        $_acl,
        $_auth, 
        $_logger,
        $_front,
        $_options,
        $_pluginBroker,
        $_request,
        $_response,
        $_user;
    
	/**
	 * Singleton instance
	 * 
	 * @return Omeka_Context
	 */
	public static function getInstance()
	{
	    if (null === self::$_instance) {
	        self::$_instance = new self();
	    }

	    return self::$_instance;
	}

	private function __construct() {}
    
    public function resetInstance()
    {
        self::$_instance = new self();
    }
    
	public function setDb(Omeka_Db $db)
	{
        //@todo GET RID OF THIS WHEN possible to be entirely dependent on Omeka_Context
        Zend_Registry::set('db', $db);  
         
	    $this->_db = $db;
	}
	
	public function getDb()
	{
	    return $this->_db;
	}
	
	/**
	 * @param string The nickname for the config set
	 * @param Zend_Config_Ini The config set
	 * @return void
	 **/
	public function setConfig($name, Zend_Config_Ini $config)
	{
	    $this->_config[$name] = $config;
	}
	
	public function getConfig($name)
	{
	    return $this->_config[$name];
	}
	
	public function setAcl(Omeka_Acl $acl)
	{
	    //@todo Remove when safe
	    Zend_Registry::set('acl', $acl);
	    
	    $this->_acl = $acl;
	}
	
	public function getAcl()
	{
	    return $this->_acl;
	}
	
	public function setAuth(Zend_Auth $auth)
	{
        //Register the Authentication mechanism to be able to share it
        Zend_Registry::set('auth', $auth);
        
        $this->_auth = $auth;	    
	}
	
	public function getAuth()
	{
	    return $this->_auth;
	}
	
	public function setLogger(Omeka_Logger $logger)
	{
	    $this->_logger = $logger;
	}
	
	public function getLogger()
	{
	    return $this->_logger;
	}
	
	public function setOptions($options)
	{
	    Zend_Registry::set('options', $options);
	    
	    $this->_options = $options;
	}
	
	public function getOptions()
	{
	    return $this->_options;
	}
	
    public function setFrontController(Zend_Controller_Front $front)
    {
        $this->_front = $front;
    }
    
    public function getFrontController()
    {
        return $this->_front;
    }
    
    public function setCurrentUser($user)
    {
        $this->_user = $user;
    }
    
    public function getCurrentUser()
    {
        return $this->_user;
    }
    
    public function setRequest($request)
    {
        $this->_request = $request;
    }
    
    public function getRequest()
    {
        return $this->_request;
    }
    
    public function setResponse($response)
    {
        $this->_response = $response;
    }
    
    public function getResponse()
    {
        return $this->_response;
    }
}
 
?>

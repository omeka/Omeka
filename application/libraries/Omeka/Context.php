<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Modelled after sfContext in Symfony framework
 *
 * Kind of a bootstrap class, this stores all (essentially global) data that
 * is needed by the application.  Examples of this include, but are not 
 * limited to: database connection, config file data, ACL, Auth, logger, etc.
 * 
 * @package Omeka
 * @author CHNM
 **/
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
        $_options = array(),
        $_pluginBroker,
        $_request,
        $_response,
        $_user,
        $_installed = true; // Omeka is thought to be installed until proven otherwise.
    
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
    
    /**
     * Verify that Omeka has been installed.  
     *
     * The criteria for Omeka being installed includes:
     * - the existence of the database options, which would be missing if the
     *  'options' table was either empty or non-existent.
     * 
     **/
    public function omekaIsInstalled()
    {        
        return $this->_installed;
    }
    
    public function setOmekaIsInstalled($flag)
    {
        $this->_installed = (boolean)$flag;
    }
    
	public function setDb(Omeka_Db $db)
	{
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
	
	public function setAcl(Zend_Acl $acl)
	{
	    $this->_acl = $acl;
	}
	
	public function getAcl()
	{
	    return $this->_acl;
	}
	
	public function setAuth(Zend_Auth $auth)
	{
        $this->_auth = $auth;	    
	}
	
	public function getAuth()
	{
	    return $this->_auth;
	}
	
	public function setLogger(Zend_Log $logger)
	{
	    $this->_logger = $logger;
	}
	
	public function getLogger()
	{
	    return $this->_logger;
	}
	
	public function setOptions($options)
	{	    
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
    
    public function getPluginBroker()
    {
        return $this->_pluginBroker;
    }
    
    public function setPluginBroker($broker)
    {
        $this->_pluginBroker = $broker;
    }
}
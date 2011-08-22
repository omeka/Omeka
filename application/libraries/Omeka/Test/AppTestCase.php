<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Abstract test case class that bootstraps the entire application.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
abstract class Omeka_Test_AppTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{   
    /**
     * Flag that determines whether the test should run against admin or public.  
     * Defaults to true (for admin). 
     *
     * @var boolean
     */
    protected $_isAdminTest = true;

    /**
     * Optimize tests by indicating whether the database was modified during 
     * the test run.  If not, the next test run can skip the Installer.
     */
    private static $_dbChanged = true;
    
    /**
     * Bootstrap the application on each test run.
     *
     * @return void
     */
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
    
    /**
     * Proxy gets to properties to allow access to bootstrap container
     * properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if ($retVal = parent::__get($property)) {
            return $retVal;
        }
        if (!isset($this->core)) {
            return;
        }
        return $this->core->getBootstrap()->getContainer()->{$property};
    }

    public function __set($property, $value)
    {
        if ($property == '_useAdminViews') {
            $this->_useAdminViewsWarning();
        }
        return parent::__set($property, $value);
    }
    
    private function _useAdminViewsWarning()
    {
        trigger_error("Omeka_Test_AppTestCase::\$_useAdminViews is " 
            . "deprecated since v1.4, please set Omeka_Test_AppTestCase::"
            . "\$_isAdminTest = false to indicate that a given test should "
            . "run against the public theme (\$_isAdminTest is " 
            . "true by default).", E_USER_WARNING);
    }
    
    /**
     * Bootstrap the application.
     *
     * @return void
     */
    public function appBootstrap()
    {        
        $this->core = new Omeka_Core('testing', array(
            'config' => CONFIG_DIR . '/' . 'application.ini'));
        
        // No idea why we actually need to add the default routes.
        $this->frontController->getRouter()->addDefaultRoutes();
        $this->frontController->setParam('bootstrap', $this->core->getBootstrap());
        $this->getRequest()->setBaseUrl('');
        // These two properties have equivalent semantic meaning, therefore should
        // be combined at some future point.
        if (isset($this->_useAdminViews)) {
            $this->_useAdminViewsWarning();
        }
        if ($this->_isAdminTest) {
            $this->_setUpThemeBootstrap('admin');
        } else {
            $this->_setUpThemeBootstrap('public');
        }
        
        $this->setUpBootstrap($this->core->getBootstrap());
        $this->core->bootstrap();
    }
    
    /**
     * Subclasses can override this to perform specialized setup on the Omeka
     * core.
     *
     * @param Zend_Application_Bootstrap $bootstrap
     * @return void
     */
    public function setUpBootstrap($bootstrap)
    {}
    
    /**
     * Reset objects that carry global state between test runs.
     *
     * @return void
     */
    public function tearDown()
    {
        // This fixes a "too many open files" error caused by hanging references
        // to the logger object somewhere in the code (could be anywhere).  
        // Since log files are only closed in log writer shutdown (only in 
        // destructor), this hanging reference keeps another file open with each 
        // test run.
        if ($this->logger instanceof Zend_Log) {
            $this->logger->__destruct();
        }    
        if ($this->db instanceof Omeka_Db) {
            Omeka_Test_Resource_Db::setDbAdapter($this->db->getAdapter());
        }
        Zend_Registry::_unsetInstance();
        Omeka_Context::resetInstance();
        Omeka_Controller_Flash::reset();
        parent::tearDown();
    }
    
    /**
     * @internal Overrides the parent behavior to enable automatic throwing of
     * exceptions from dispatching.
     * @param string $url
     * @param boolean $throwExceptions
     * @return void
     */
    public function dispatch($url = null, $throwExceptions = true)
    {
        parent::dispatch($url);
        if ($throwExceptions) {
            if (isset($this->request->error_handler)) {
                throw $this->request->error_handler->exception;
            }
        }        
    }
    

    public static function dbChanged($flag = null)
    {
        if ($flag !== null) {
            self::$_dbChanged = (boolean)$flag;
        }
        return self::$_dbChanged;
    }

    /**
     * Trick the environment into thinking that a user has been authenticated.
     *
     * @param User $user
     * @return void
     */
    protected function _authenticateUser(User $user)
    {
        if (!$user->exists()) {
            throw new InvalidArgumentException("User is not persistent in db.");
        }
        $bs = $this->core->getBootstrap();
        $bs->auth->getStorage()->write($user->id);
        $bs->currentUser = $user;
        $bs->getContainer()->currentuser = $user;
        $aclHelper = Zend_Controller_Action_HelperBroker::getHelper('Acl'); 
        $aclHelper->setCurrentUser($user); 
        
    }
    
    /**
     * Get the user that is installed by default.
     *
     * @return User
     */
    protected function _getDefaultUser()
    {
        return $this->db->getTable('User')->find(Omeka_Test_Resource_Db::DEFAULT_USER_ID);
    }
    
    /**
     * Set up the bootstrap differently depending on whether the test is meant
     * for the public or admin themes.
     */
    private function _setUpThemeBootstrap($themeType)
    {
        switch ($themeType) {
            case 'admin':
                $this->frontController->setParam('admin', true);
                $this->frontController->registerPlugin(new Omeka_Controller_Plugin_Admin);
                $this->core->getBootstrap()->setOptions(array(
                    'resources' => array(
                        'theme' => array(
                            'basePath' => ADMIN_THEME_DIR,
                            'webBasePath' => '/admin/themes/'
                        )
                    )
                ));
                break;
            case 'public':
                $this->core->getBootstrap()->setOptions(array(
                    'resources' => array(
                        'theme' => array(
                            'basePath' => PUBLIC_THEME_DIR,
                            'webBasePath' => '/themes/'
                        )
                    )
                ));
                break;
            default:
                throw new InvalidArgumentException("Invalid theme type given.");
                break;
        }
    }
}

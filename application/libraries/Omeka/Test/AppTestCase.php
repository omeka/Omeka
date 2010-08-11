<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Abstract test case class that bootstraps the entire application.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009-2010
 */
abstract class Omeka_Test_AppTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{   
    /**
     * Flag that should be set by subclasses that test the admin theme.
     *
     * @var boolean
     */
    protected $_isAdminTest = false;
    
    /**
     * Whether the view should attempt to load admin scripts for 
     * testing purposes.  Defaults to true.
     *
     * @var boolean
     */
    protected $_useAdminViews = true;
    
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
        return $this->core->getBootstrap()->getContainer()->{$property};
    }
    
    /**
     * Bootstrap the application.
     *
     * @return void
     */
    public function appBootstrap()
    {
        // Must happen before all other bootstrapping.
        if ($this->_isAdminTest) {
            $this->_setupAdminTest();
        }
        
        $this->core = new Omeka_Core('testing', array(
            'config' => CONFIG_DIR . DIRECTORY_SEPARATOR . 'application.ini'));
        
        // No idea why we actually need to add the default routes.
        $this->frontController->getRouter()->addDefaultRoutes();
        $this->frontController->setParam('bootstrap', $this->core->getBootstrap());
        $this->getRequest()->setBaseUrl('');
        $this->setUpBootstrap($this->core->getBootstrap());
        $this->core->bootstrap();
        if ($this->_useAdminViews) {
            $this->_useAdminViews();
        }
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
    
    /**
     * Increment assertion count
     *
     * @todo COPIED FROM ZEND FRAMEWORK 1.10, REMOVE AFTER UPGRADING TO THAT
     * VERSION.
     * @return void
     */
    protected function _incrementAssertionCount()
    {
        $stack = debug_backtrace();
        foreach (debug_backtrace() as $step) {
            if (isset($step['object'])
                && $step['object'] instanceof PHPUnit_Framework_TestCase
            ) {
                if (version_compare(PHPUnit_Runner_Version::id(), '3.3.0', 'lt')) {
                    break;
                } elseif (version_compare(PHPUnit_Runner_Version::id(), '3.3.3', 'lt')) {
                    $step['object']->incrementAssertionCounter();
                } else {
                    $step['object']->addToAssertionCount(1);
                }
                break;
            }
        }
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
     * Add admin view scripts to search path.
     *
     * @return void
     */
    protected function _useAdminViews()
    {
        $this->view = Zend_Registry::get('view');
        $this->view->addScriptPath(ADMIN_THEME_DIR . DIRECTORY_SEPARATOR . 'default');
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
     * Set up for testing the admin interface.
     *
     * @internal Necessary because admin and public have 2 separate bootstraps.
     * @return void
     */
    private function _setupAdminTest()
    {
        // define('THEME_DIR', ADMIN_DIR . DIRECTORY_SEPARATOR . 'themes');
        $this->frontController->registerPlugin(new Omeka_Controller_Plugin_Admin);
    }
}

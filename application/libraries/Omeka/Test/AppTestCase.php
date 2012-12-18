<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Abstract test case class that bootstraps the entire application.
 * 
 * @package Omeka\Test
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
        if (!isset($this->application)) {
            return;
        }
        return $this->application->getBootstrap()->getResource($property);
    }

    /**
     * Bootstrap the application.
     *
     * @return void
     */
    public function appBootstrap()
    {        
        $this->application = new Omeka_Application('testing', array(
            'config' => CONFIG_DIR . '/' . 'application.ini'));
        
        // No idea why we actually need to add the default routes.
        $this->frontController->getRouter()->addDefaultRoutes();
        $this->frontController->setParam('bootstrap', $this->application->getBootstrap());
        $this->getRequest()->setBaseUrl('');

        if ($this->_isAdminTest) {
            $this->_setUpThemeBootstrap('admin');
        } else {
            $this->_setUpThemeBootstrap('public');
        }
        
        $this->setUpBootstrap($this->application->getBootstrap());
        $this->application->bootstrap();
    }
    
    /**
     * Subclasses can override this to perform specialized setup on the Omeka
     * application.
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
            $this->db->rollBack();
        }
        Zend_Registry::_unsetInstance();

        unset($this->bootstrap);
        unset($this->application);
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
        $bs = $this->application->getBootstrap();
        $bs->auth->getStorage()->write($user->id);
        $bs->currentUser = $user;
        $bs->getContainer()->currentuser = $user;
        $aclHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Acl');
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
                $this->application->getBootstrap()->setOptions(array(
                    'resources' => array(
                        'theme' => array(
                            'basePath' => ADMIN_THEME_DIR,
                            'webBasePath' => '/admin/themes/'
                        )
                    )
                ));
                break;
            case 'public':
                $this->application->getBootstrap()->setOptions(array(
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

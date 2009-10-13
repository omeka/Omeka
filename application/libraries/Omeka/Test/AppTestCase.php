<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
abstract class Omeka_Test_AppTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{   
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->core = new Omeka_Core('testing', array(
            'config' => CONFIG_DIR . DIRECTORY_SEPARATOR . 'application.ini'));
        
        // No idea why we actually need to add the default routes.
        $this->frontController->getRouter()->addDefaultRoutes();
        
        $this->setUpBootstrap($this->core->getBootstrap());
        $this->core->bootstrap();
    }
    
    public function setUpBootstrap($bootstrap)
    {}

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
        Omeka_Context::resetInstance();
        parent::tearDown();
    }
}

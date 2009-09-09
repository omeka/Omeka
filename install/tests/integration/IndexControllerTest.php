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
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
    
    public function appBootstrap()
    {
        // Include the install app's bootstrap.
        include '../index.php';
        $this->app = $application;
        
        // For some reason we need this or tests fail.  Not sure why it's not automatically registered.
        Zend_Controller_Front::getInstance()->setParam('bootstrap', $this->app->getBootstrap());
    }
    
    public function assertPreConditions()
    {
        // Make sure that we haven't initialized a database connection prior
        // to running these tests.
        // Fake a database connection to convince the installer that Omeka is already installed.
        $db = $this->app->getBootstrap()->getResource('db');
        $this->assertNull($db);
    }
    
    public function testInstallerMissingDbIniFile()
    {
        // Create a mock db resource, but have it throw a Zend_Config_Exception.
        // That will convince the installer that there is no config file.
        $mockDbResource = $this->getMockBootstrapResource('Db', null);
        $mockDbResource->expects($this->once())->method('init')->will($this->throwException(new Zend_Config_Exception("Missing database ini file!")));
        
        $this->app->getBootstrap()->registerPluginResource($mockDbResource);
        $this->dispatch('');
        $this->assertController('index');
        $this->assertAction('fatal-error');
    }
    
    public function testOmekaIsAlreadyInstalled()
    {
        // REMEMBER: Omeka_Db does not have fetchAll() or fetchOne() methods, so
        // you have to declare these here.
        $db = $this->getMock('Omeka_Db', array('fetchAll', 'fetchOne'), array(), '', false);
        $db->prefix = 'omeka_';
        
        // Quick way of faking that the options table has 10 options in it.
        // This should probably be replaced with a dataset fixture.
        $db->expects($this->any())->method('fetchAll')->will($this->returnValue(array('options')));
        $db->expects($this->any())->method('fetchOne')->will($this->returnValue(10));
        
        $mockDbResource = $this->getMockBootstrapResource('Db', $db);
        $this->app->getBootstrap()->registerPluginResource($mockDbResource);
        
        $this->dispatch('');
        // var_dump($this->request);exit;
        // echo $this->response->getBody();exit;
        $this->assertController('index');
        $this->assertAction('already-installed');
    }
    
    public function testOmekaInstallerHasErrors()
    {
        // Since we can only mock the database resource (at this point), the 
        // only error we can/need to test is having an inadequate MySQL version.
        $db = $this->getMock('Omeka_Db', array('fetchAll', 'fetchOne', 'getAdapter'), array(), '', false);
        $dbAdapter = $this->getMock('Zend_Db_Adapter_Mysqli', array(), array(), '', false);
        
        $db->expects($this->any())->method('getAdapter')->will($this->returnValue($dbAdapter));
        $dbAdapter->expects($this->any())->method('getServerVersion')->will($this->returnValue('4.0'));
        
        $this->app->getBootstrap()->registerPluginResource($this->getMockBootstrapResource('Db', $db));
        
        $this->dispatch('');        
        $this->assertController('index');
        $this->assertAction('errors');
    }
    
    public function testFormRequiredFields()
    {
        
    }
    
    public function testSuccessfulInstall()
    {   
        // Clear out the database prior to running this particular test.
        $this->_dropTables();
        
        // Point us at an empty database.
        $dbResource = new Omeka_Core_Resource_Db;
        $dbResource->setinipath(APPLICATION_PATH . '/tests/db.ini');
        $this->app->getBootstrap()->registerPluginResource($dbResource);
        
        $this->request->setMethod('post');
        $this->request->setPost(array(
        'username'                      => 'foobar',
        'password'                      => 'foobar',
        'super_email'                   => 'testemail@omeka.org',
        'administrator_email'           => 'testemail@omeka.org',
        'site_title'                    => 'Omeka Test Whatever',
        'description'                   => 'Omeka Site Description',
        'copyright'                     => 'Omeka Copyright Info',
        'author'                        => 'Omeka Author Info',
        'fullsize_constraint'           => '800',
        'thumbnail_constraint'          => '200',
        'square_thumbnail_constraint'   => '200',
        'per_page_admin'                => '10',
        'per_page_public'               => '10',
        'show_empty_elements'           => '0',
        'path_to_convert'               => '/foo/bar',
        'install_submit'                => 'Install'
        ));
        
        $this->dispatch('');
        $this->assertRedirectTo('/index/success');
    }
    
    // COPIED DIRECTLY FROM Omeka_Test_Bootstrapper.
    public static function getMockBootstrapResource($resourceName, $returnVal)
    {
        // Create a mock resource object for each of the desired whatevers.
       $mockClassName = 'TestMock_' . $resourceName;
       $methods = array('init');
       $className = 'Zend_Application_Resource_ResourceAbstract';
       $callOriginalConstructor = false;
       $callOriginalClone = false;
       $callAutoload = true;
       if (!class_exists($mockClassName)) {
           $mockDefinition = PHPUnit_Framework_MockObject_Mock::generate(
                $className,
                $methods,
                $mockClassName,
                $callOriginalConstructor,
                $callOriginalClone,
                $callAutoload);
       }
   
   
       // Instantiate the mock resource and tell it to always return the value
       // we have specified via the 'return' key of the original array.
       $mockResourceObj = new $mockClassName;   
       $mockResourceObj->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
                       ->method('init')
                       ->will(new PHPUnit_Framework_MockObject_Stub_Return($returnVal));
                   
       // Make sure it also thinks it has the correct resource name.
       $mockResourceObj->_explicitType = $resourceName;
   
       return $mockResourceObj;    
    }
    
    /**
     * Drop the database tables.  Should only be used for the testing environment
     * db.ini file.
     */
    private function _dropTables()
    {
        $dbResource = new Omeka_Core_Resource_Db;
        $dbResource->setinipath(APPLICATION_PATH . '/tests/db.ini');
        $db = $dbResource->init();        
        $dropSql = "DROP TABLE IF EXISTS
            `{$db->prefix}collections`, 
            `{$db->prefix}data_types`, 
            `{$db->prefix}elements`, 
            `{$db->prefix}element_sets`, 
            `{$db->prefix}element_texts`, 
            `{$db->prefix}entities`, 
            `{$db->prefix}entities_relations`, 
            `{$db->prefix}entity_relationships`, 
            `{$db->prefix}files`, 
            `{$db->prefix}items`, 
            `{$db->prefix}item_types`, 
            `{$db->prefix}item_types_elements`, 
            `{$db->prefix}mime_element_set_lookup`, 
            `{$db->prefix}options`, 
            `{$db->prefix}plugins`, 
            `{$db->prefix}processes`, 
            `{$db->prefix}record_types`, 
            `{$db->prefix}taggings`, 
            `{$db->prefix}tags`, 
            `{$db->prefix}users`, 
            `{$db->prefix}users_activations`";
        $db->query($dropSql);
    }
}

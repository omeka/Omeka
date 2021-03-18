<?php
class Omeka_Application_Resource_JobsTest extends Omeka_Test_TestCase
{
    /**
     * Warning: this code has been adapted from Zend Framework's 
     * Zend_Application_Resource_FrontcontrollerTest.
     */
    public function setUpLegacy()
    {
        Zend_Registry::_unsetInstance();
        $this->application = new Zend_Application('testing');
        $this->bootstrap = new Omeka_Application_Bootstrap_Mock($this->application);
        $this->config = new Zend_Config(array(
            'jobs' => array(
                'dispatcher' => array(
                    'default' => 'Omeka_Job_Dispatcher_Adapter_Array',
                    'defaultOptions' => array(),
                )
            )
        ), true);
        $this->bootstrap->setResource('config', $this->config);
        $this->user = new stdClass;
        $this->bootstrap->setResource('currentuser', $this->user);
        $this->db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $this->bootstrap->setResource('db', $this->db);
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testErrorOnInvalidAdapterClassName()
    {
        $this->setExpectedException('Omeka_Application_Resource_Jobs_InvalidAdapterException');
        $resource = new Omeka_Application_Resource_Jobs();
        $resource->setBootstrap($this->bootstrap);
        $this->config->jobs->dispatcher->default = 'foobar';
        $resource->init();
    }

    public function testLoadDefaultDispatcher()
    {
        $resource = new Omeka_Application_Resource_Jobs();
        $resource->setBootstrap($this->bootstrap);
        unset($this->config->jobs);
        // The fact that this doesn't die when there are no config settings
        // means that it set up a valid dispatcher adapter.
        $dispatcher = $resource->init();
        $this->assertEquals('Omeka_Job_Dispatcher_Default', get_class($dispatcher));
    }

    public function testErrorOnMissingAdapterClassInterface()
    {
        $this->setExpectedException('Omeka_Application_Resource_Jobs_InvalidAdapterException');
        $resource = new Omeka_Application_Resource_Jobs();
        $resource->setBootstrap($this->bootstrap);
        // A class name that exists but does not implement the correct
        // interface.
        $this->config->jobs->dispatcher->default = 'stdClass';
        $dispatcher = $resource->init();
    }

    public function testFactoryAndDispatcherRegistered()
    {
        $resource = new Omeka_Application_Resource_Jobs();
        $resource->setBootstrap($this->bootstrap);
        $dispatcher = $resource->init();
        $this->assertTrue(Zend_Registry::isRegistered('job_dispatcher'));
        $this->assertTrue(Zend_Registry::isRegistered('job_factory'));
    }
}

class Omeka_Application_Bootstrap_Mock extends Zend_Application_Bootstrap_BootstrapAbstract
{
    private $_mockResources = array();

    public function run()
    {
    }

    public function setResource($name, $resource)
    {
        $this->_mockResources[$name] = $resource;
    }

    public function getResource($name)
    {
        if (!$this->hasResource($name)) {
            throw new InvalidArgumentException("Resource named '$name' is not available.");
        }
        return $this->_mockResources[$name];
    }

    public function hasResource($name)
    {
        return array_key_exists(strtolower($name), $this->_mockResources);
    }

    protected function _bootstrap($resource = null)
    {
        if (!$this->hasResource($resource)) {
            throw new InvalidArgumentException("Resource named '$resource' is not available.");
        }
    }
}

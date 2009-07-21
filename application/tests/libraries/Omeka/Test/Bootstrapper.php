<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

/**
 * The point of this class is to separate out the behavior that bootstraps
 * test cases, so that test writers don't have to depend on a brittle 
 * inheritance hierarchy.  For example, some test cases may need the full MVC
 * approach of Zend_Test_PhpUnit_ControllerTestCase, whereas other test cases
 * may just want to bootstrap partial phases (e.g. testing models, helpers, 
 * background scripts, etc.).  
 * 
 * Relying on a complex inheritance hierarchy increases the baseline complexity
 * of tests and makes them harder to fully comprehend.
 *
 * The only requirement of test cases that use this class is that they have a 
 * public method named 'setUpBootstrap($bootstrap)'.  This allows fine-grained
 * configuration for test cases.
 * 
 * @package Omeka_Test
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Test_Bootstrapper
{
    private $_testCase;
    
    public function __construct(PHPUnit_Framework_TestCase $testCase)
    {
        $this->_testCase = $testCase;
    }
    
    public function bootstrap()
    {
        $core = new Omeka_Core('testing', array(
            'config' => CONFIG_DIR . DIRECTORY_SEPARATOR . 'application.ini'));
        
        $bootstrap = $core->getBootstrap();
        
        if (method_exists($this->_testCase, 'setUpBootstrap')) {
            $this->_testCase->setUpBootstrap($bootstrap);  
        }
        
        $core->bootstrap();
        
        // Sets a public property on the test case so that tests can access this property.
        $this->_testCase->core = $bootstrap;
        return $core;
    }
    
    /**
     * Example usage when configuring bootstrap:
     * <code>
     * $mockDbResource = $this->_getMockBootstrapResource('Db', $this->_getMockDbWithMockTables());
     * $this->core->registerPluginResource($mockDbResource);
     * </code>
     * 
     * @param string
     * @param mixed
     * @return mixed
     **/
    static function getMockBootstrapResource($resourceName, $returnVal)
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
}

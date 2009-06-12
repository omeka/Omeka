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
        $core = new Omeka_Core(null);
        $core->setOptions(array(
            'pluginpaths'=>
                array(
                    'Omeka_Core_Resource' => LIB_DIR . '/Omeka/Core/Resource/',
                    'Omeka_Test_Resource' => TEST_LIB_DIR . '/Omeka/Test/Resource/')));
        
        if (method_exists($this->_testCase, 'setUpBootstrap')) {
            $this->_testCase->setUpBootstrap($core);  
        }
        
        $core->bootstrap();
        
        // Sets a public property on the test case so that tests can access this property.
        $this->_testCase->core = $core;
        return $core;
    }    
}

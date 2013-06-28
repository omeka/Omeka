<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test ACL integration with controllers.  
 * 
 * Verify that the ACL controller helper is registered and operating properly.
 * Verify the set of basic ACL permissions.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Controllers_AclTest extends Omeka_Test_AppTestCase
{   
    protected $_isAdminTest = false;
    
    public function setUp()
    {
        parent::setUp();
        $this->aclHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('acl');
    }

    public function assertPreConditions()
    {
        $this->assertNull($this->application->getBootstrap()->getResource('Currentuser'));
        $this->assertEquals('Omeka_Controller_Action_Helper_Acl', get_class($this->aclHelper));
    }
        
    public function testAclHelperAllowsAccessForDefinedResource()
    {
        $this->assertTrue($this->acl->has('Items'));
        $this->dispatch('items');
        $this->assertController('items');
        $this->assertAction('browse');
        $this->assertTrue($this->aclHelper->isAllowed('browse', 'Items'));
    }
        
    public function testAclHelperAllowsAccessForUndefinedResource()
    {
        $this->assertFalse($this->acl->has('Index'));
        $this->dispatch('');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertTrue($this->aclHelper->isAllowed('index', 'Index'));
    }
    
    public function testAclHelperBlocksAccess()
    {
        $this->assertTrue($this->acl->has('Users'));
        $this->dispatch('users');
        $this->_assertLoginRequired();
        $this->assertFalse($this->aclHelper->isAllowed('browse', 'Users'));
    }
    
    /**
     * The ACL action helper dispatches to users/login when there
     * is no authenticated user.  Previous behavior was to always dispatch to
     * error/forbidden.
     */
    private function _assertLoginRequired()
    {
        $this->assertController('users');
        $this->assertAction('login');
    } 
   
    private function _assertAccessForbidden()
    {
        $this->assertController('error');
        $this->assertAction('forbidden');        
    }
}

<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Test ACL integration with controllers.  
 * 
 * Verify that the ACL controller helper is registered and operating properly.
 * Verify the set of basic ACL permissions.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2010
 **/
class Controllers_AclTest extends Omeka_Test_AppTestCase
{   
    protected $_useAdminViews = false;
    
    public function setUp()
    {
        parent::setUp();
        $this->aclHelper = Zend_Controller_Action_HelperBroker::getHelper('acl');
    }
             
    public function assertPreConditions()
    {
        $this->assertFalse($this->core->getBootstrap()->getResource('Currentuser'));
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
    
    /**
     * This test case will be invalidated in 2.0.  Note that this should be 
     * removed or skipped in future versions of the software.
     * 
     * Should be removed because privileges are no longer defined in Omeka_Acl
     * in Omeka 2.0.
     */
    public function testAclHelperAllowsAccessForDefinedResourceWithUndefinedPrivilege()
    {
        $this->assertTrue($this->acl->has('Items'));
        $this->assertFalse($this->acl->get('Items')->has('foobar'));
        $this->assertTrue($this->aclHelper->isAllowed('foobar', 'Items'));
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
        $this->assertTrue($this->acl->has('ElementSets'));
        $this->dispatch('element-sets');
        $this->_assertAccessForbidden();
        $this->assertFalse($this->aclHelper->isAllowed('browse', 'ElementSets'));
    }
    
    private function _assertAccessForbidden()
    {
        $this->assertController('error');
        $this->assertAction('forbidden');        
    }
}
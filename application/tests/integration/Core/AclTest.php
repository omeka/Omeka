<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Test default ACL configuration.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Core_AclTest extends PHPUnit_Framework_TestCase
{
    private $_acl;
    
    /**
     * Include the defined ACL, exactly the way Omeka loads it by default.
     * 
     * @return void
     **/
    public function setUp()
    {
        include CORE_DIR . DIRECTORY_SEPARATOR . 'acl.php';
        $this->_acl = $acl;
    }
        
    public function testRestrictionsForNonAuthenticatedUsers()
    {
       $this->assertFalse($this->_acl->isAllowed(null, 'Items', 'add'));
       $this->assertFalse($this->_acl->isAllowed(null, 'Collections', 'add'));
       $this->assertFalse($this->_acl->isAllowed(null, 'ItemTypes', 'add')); 
       $this->assertFalse($this->_acl->isAllowed(null, 'Themes', 'config')); 
       $this->assertFalse($this->_acl->isAllowed(null, 'Themes', 'browse'));
       $this->assertFalse($this->_acl->isAllowed(null, 'Themes', 'switch'));
       $this->assertFalse($this->_acl->isAllowed(null, 'Settings', 'edit'));
    }
    
    public function testRestrictionsForContributors()
    {
        $this->assertTrue($this->_acl->isAllowed('contributor', 'Items', 'add'));
        $this->assertFalse($this->_acl->isAllowed('contributor', 'ItemTypes', 'add'));
        $this->assertFalse($this->_acl->isAllowed('contributor', 'Plugins', 'add'));
    }
    
    public function testRestrictionsForAdmins()
    {
        $this->assertFalse($this->_acl->isAllowed('admin', 'Settings', 'edit'));
        $this->assertFalse($this->_acl->isAllowed('admin', 'Security', 'edit'));
        $this->assertFalse($this->_acl->isAllowed('admin', 'Themes'));
        $this->assertFalse($this->_acl->isAllowed('admin', 'Plugins'));
        $this->assertFalse($this->_acl->isAllowed('admin', 'ElementSets'));
        $this->assertFalse($this->_acl->isAllowed('admin', 'Users'));
        
        $this->assertTrue($this->_acl->isAllowed('admin', 'Items', 'editAll'));
        $this->assertTrue($this->_acl->isAllowed('admin', 'Items', 'deleteAll'));
    }
    
    public function testRestrictionsForResearchers()
    {
        $this->assertTrue($this->_acl->isAllowed('researcher', 'Items', 'showNotPublic'));
        $this->assertFalse($this->_acl->isAllowed('researcher', 'Items', 'editAll'));
        
    }
}

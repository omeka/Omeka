<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test default ACL configuration.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Core_AclTest extends PHPUnit_Framework_TestCase
{
    private $_acl;
    
    /**
     * Include the defined ACL, exactly the way Omeka loads it by default.
     * 
     * @return void
     */
    public function setUp()
    {
        include CORE_DIR . '/acl.php';
        $this->_acl = $acl;
    }

    public function tearDown()
    {
        unset($this->_acl);
    }

    public static function acl()
    {
        return array(
            // $isAllowed, $role, $resource, $privilege
            array(false, null, 'Items', 'add'),
            array(false, null, 'Collections', 'add'),
            array(false, null, 'ItemTypes', 'add'),
            array(false, null, 'Themes', 'config'), 
            array(false, null, 'Themes', 'browse'),
            array(false, null, 'Themes', 'switch'),
            array(false, null, 'Settings', 'edit'),
            array(false, null, 'Items', 'add'),
            array(false, null, 'Users', 'edit'),
            array(false, null, 'Users', 'browse'),
            array(false, null, 'Users', 'delete'),
            array(true, 'contributor', 'Items', 'add'),
            array(false, 'contributor', 'ItemTypes', 'add'),
            array(false, 'contributor', 'Plugins', 'add'),
            array(false, 'admin', 'Settings', 'edit'),
            array(false, 'admin', 'Security', 'edit'),
            array(false, 'admin', 'Themes'),
            array(false, 'admin', 'Plugins'),
            array(false, 'admin', 'ElementSets'),
            array(false, 'admin', 'Users'),
            array(true, 'admin', 'Items', 'editAll'),
            array(true, 'admin', 'Items', 'deleteAll'),
            array(true, 'admin', 'Items', 'add'),
            array(true, 'admin', 'Items', 'browse'),
            array(true, 'admin', 'Items', 'edit'),
            array(false, 'admin', 'Users', 'browse'),
            array(true, 'researcher', 'Items', 'showNotPublic'),
            array(false, 'researcher', 'Items', 'editAll'),
        );
    }

    /**
     * @dataProvider acl
     */
    public function testAcl($isAllowed, $role, $resource, $privilege = null)
    {
        $this->assertEquals($isAllowed, 
            $this->_acl->isAllowed($role, $resource, $privilege));
    }
}

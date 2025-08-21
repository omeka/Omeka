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
class Core_AclTest extends Omeka_Test_TestCase
{
    private $_acl;

    /**
     * Include the defined ACL, exactly the way Omeka loads it by default.
     */
    public function setUpLegacy()
    {
        $acl = new Omeka_Application_Resource_Acl;
        $this->_acl = $acl->getAcl();
    }

    public function tearDownLegacy()
    {
        unset($this->_acl);
    }

    public static function acl()
    {
        return [
            // $isAllowed, $role, $resource, $privilege
            [false, null, 'Items', 'add'],
            [false, null, 'Collections', 'add'],
            [false, null, 'ItemTypes', 'add'],
            [false, null, 'Themes', 'config'],
            [false, null, 'Themes', 'browse'],
            [false, null, 'Themes', 'switch'],
            [false, null, 'Settings', 'edit'],
            [false, null, 'Items', 'add'],
            [false, null, 'Users', 'edit'],
            [false, null, 'Users', 'browse'],
            [false, null, 'Users', 'delete'],
            [true, 'contributor', 'Items', 'add'],
            [false, 'contributor', 'ItemTypes', 'add'],
            [false, 'contributor', 'Plugins', 'add'],
            [false, 'admin', 'Settings', 'edit'],
            [false, 'admin', 'Security', 'edit'],
            [false, 'admin', 'Themes'],
            [false, 'admin', 'Plugins'],
            [false, 'admin', 'ElementSets'],
            [false, 'admin', 'Users'],
            [true, 'admin', 'Items', 'editAll'],
            [true, 'admin', 'Items', 'deleteAll'],
            [true, 'admin', 'Items', 'add'],
            [true, 'admin', 'Items', 'browse'],
            [true, 'admin', 'Items', 'edit'],
            [false, 'admin', 'Users', 'browse'],
            [true, 'researcher', 'Items', 'showNotPublic'],
            [false, 'researcher', 'Items', 'editAll'],
        ];
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

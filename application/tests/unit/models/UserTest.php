<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test the User model.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->user = new User($this->db);
    }
    
    public function testGetSetEntityProperties()
    {
        $this->user->getEntity()->email = 'foobar@example.com';
        $this->assertEquals('foobar@example.com', $this->user->email);
    }
}

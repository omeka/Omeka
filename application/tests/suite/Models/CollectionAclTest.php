<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Tests
 */

/**
 * @package Omeka
 * @subpackage Tests
 */
class Models_CollectionAclTest extends Omeka_Test_AppTestCase
{
    private $_users;
    private $_collections;

    public function setUp()
    {
        parent::setUp();

        $super = new User;
        $super->role = 'super';
        $super->id = 1;
        $contributor = new User;
        $contributor->role = 'contributor';
        $contributor->id = 2;
        $researcher = new User;
        $researcher->role = 'researcher';
        $researcher->id = 3;

        $this->_users = array(
            'super' => $super,
            'contributor' => $contributor,
            'researcher' => $researcher
        );

        $this->_collections = array(
            'addedBySelf' => $this->_getMockCollection(true),
            'notAddedBySelf' => $this->_getMockCollection(false),
        );
    }

    public function tearDown()
    {
        release_object($this->_users);
        parent::tearDown();
    }

    /**
     * Test the ownership ACL for a specific user.
     *
     * @dataProvider userResourceProvider
     * @param string $userKey
     * @param boolean $whenOwner
     * @param boolean $whenNotOwner
     * @param boolean $generally
     */
    public function testOwnershipAcl($userKey, $whenOwner, $whenNotOwner, $generally)
    {
        $user = $this->_users[$userKey];

        foreach ($this->_collections as $collectionKey => $collection) {
            if ($collectionKey == 'addedBySelf') {
                $expectation = $whenOwner;
            } else {
                $expectation = $whenNotOwner;
            }

            $assertionType = $expectation ? 'can' : 'cannot';

            $this->assertEquals($expectation,
                $this->acl->isAllowed($user, $collection, 'edit'),
                "Failed asserting that $userKey $assertionType edit collection $collectionKey");
            $this->assertEquals($expectation,
                $this->acl->isAllowed($user, $collection, 'delete'),
                "Failed asserting that $userKey $assertionType delete collection $collectionKey.");
        }
        $this->assertEquals($generally, $this->acl->isAllowed($user, 'Collections', 'edit'));
        $this->assertEquals($generally, $this->acl->isAllowed($user, 'Collections', 'delete'));
    }

    public function userResourceProvider()
    {
        return array(
            // $userKey, $whenOwner, $whenNotOwner, $generally
            array('super', true, true, true),
            array('contributor', true, false, true),
            array('researcher', false, false, false)
        );
    }

    /**
     * Get a mocked Collection object with stubs for the methods needed by the
     * ACL.
     *
     * @param boolean $addedBySelf
     * @return Collection mock Collection object
     */
    private function _getMockCollection($addedBySelf)
    {
        $collection = $this->getMock('Collection', array('getResourceId', 'isOwnedBy'));
        $collection->expects($this->any())
             ->method('getResourceId')
             ->will($this->returnValue('Collections'));
        $collection->expects($this->any())
             ->method('isOwnedBy')
             ->will($this->returnValue($addedBySelf));
        return $collection;
    }
}

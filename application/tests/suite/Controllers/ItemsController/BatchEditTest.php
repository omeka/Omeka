<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests for batch editing of Items.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Controller_ItemsController_BatchEditTest extends Omeka_Test_AppTestCase
{
    private $_users;
    private $_items;

    public function setUp()
    {
        parent::setUp();
        $this->_authenticateUser($this->_getDefaultUser());

        $userRoles = array('admin', 'contributor', 'researcher');

        foreach ($userRoles as $role) {
            $user = new User;

            $userData = array(
                'role'          => $role,
                'username'      => $role,
                'name'          => "User $role",
                'email'         => $role .'@example.com'
            );

            $user->setPostData($userData);
            $user->save();

            $this->_users[$role] = $user;
        }
                
        foreach ($userRoles as $role) {
            if ($role != 'researcher') {
                $item = $this->_createItem($role);
                $this->_items[$role] = $item;
            }
        }

        $this->_users['super'] = $this->_getDefaultUser();

        $this->_authenticateUser($this->_getDefaultUser());
    }

    public function tearDown()
    {
        release_object($this->_users);
        release_object($this->_items);
        parent::tearDown();
    }

    /**
     * Data provider to check whether a given role can delete all items.
     */
    public function userRoleCanDeleteProvider()
    {
        // Researcher is not included because that particular test will 
        // always fail.
        return array(
            array('contributor', false),
            array('admin', true),
            array('super', true)
        );
    }

    /**
     * Data provider to check whether a given role can access the batch delete
     * action.
     */
    public function userRoleCanAccessBatchEditProvider()
    {
        return array(
            array('researcher', false),
            array('contributor', true),
            array('admin', true),
            array('super', true)
        );
    }
    
    public function testBatchEditWithoutItems()
    {
        $this->dispatch('/items/batch-edit');
        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        $this->assertContains("You must choose some items to batch edit.", $messages['error']);
    }

    /**
     * @expectedException Omeka_Controller_Exception_403
     */
    public function testBatchEditWithoutHash()
    {
        $post = array(
            'items' => array('2', '3'),
            'metadata'  => array(
                'item_type_id' => 1,
                'tags'  => 'lorem,ipsum,dolor'
            ),
        );

        $this->_makePost($post);
        $this->dispatch('/items/batch-edit-save');
    }

    /**
     * @dataProvider userRoleCanAccessBatchEditProvider
     */
    public function testBatchEditActionAccess($userRole, $succeeds)
    {
        $itemIds = array();

        foreach ($this->_items as $item) {
            $itemIds[] = $item->id;
        }

        $this->request->setPost(array(
            'items' => $itemIds,
        ));

        $this->request->setMethod('GET');
        $this->_authenticateUser($this->_users[$userRole]);
        $this->dispatch('/items/batch-edit');
        
        if ($succeeds) {
            $this->assertController('items');
            $this->assertAction('batch-edit');
        } else {
            $this->assertController('error');
            $this->assertAction('forbidden');
        }
    }

    /**
     * @dataProvider userRoleCanAccessBatchEditProvider
     */
    public function testBatchEditSaveAccess($userRole, $succeeds)
    {
        $hash = new Zend_Form_Element_Hash('batch_edit_hash');
        $hash->initCsrfToken();
        $this->request->setMethod('post');
        $this->request->setPost(array(
            'batch_edit_hash' => $hash->getHash(),
        ));
        $this->_authenticateUser($this->_users[$userRole]);
        $this->dispatch('/items/batch-edit-save');
        if ($succeeds) {
            $this->assertController('items');
            $this->assertAction('batch-edit-save');
        } else {
            $this->assertController('error');
            $this->assertAction('forbidden');
        }
    }

    public function testBatchEditSaveWithNoPost()
    {
        $this->dispatch('/items/batch-edit-save');
        $this->assertController('error');
        $this->assertAction('method-not-allowed');
    }
    
    public function testBatchEditSaveSuperUser()
    {
        $this->_authenticateUser($this->_getDefaultUser());

        $itemIds = array();

        foreach ($this->_items as $item) {
            $itemIds[] = $item->id;
        }

        $this->_makePost();

        $this->dispatch('/items/batch-edit-save');
        $this->assertController('items');
        $this->assertAction('batch-edit-save');
        
        foreach ($itemIds as $id) {
            $item = $this->db->getTable('Item')->find($id);
            $this->assertTrue($item->isPublic());
            $this->assertTrue($item->isFeatured());
            $this->assertEquals($item->item_type_id, '1');
            $this->assertEquals(3, count($item->getTags()));
        }

        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        $this->assertContains("The items were successfully changed!", $messages['success']);
    }

    public function testBatchEditSaveAdminUser()
    {
        $this->_authenticateUser($this->_users['admin']);

        $itemIds = array();

        foreach ($this->_items as $item) {
            $itemIds[] = $item->id;
        }

        $this->_makePost();

        $this->dispatch('/items/batch-edit-save');

        foreach ($itemIds as $id) {
            $item = $this->db->getTable('Item')->find($id);
            $this->assertTrue($item->isPublic());
            $this->assertTrue($item->isFeatured());
            $this->assertEquals($item->item_type_id, '1');
            $this->assertEquals(3, count($item->getTags()));
        }

        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        $this->assertContains("The items were successfully changed!", $messages['success']);
    }
    
    public function testBatchEditContributorUserSaveAllowedData()
    {
        $this->_authenticateUser($this->_users['contributor']);

        $hash = new Zend_Form_Element_Hash('batch_edit_hash');
        $hash->initCsrfToken();
        
        $itemIds = array($this->_items['contributor']->id);

        $post = array(
            'items' => $itemIds,
            'metadata'  => array(
                'item_type_id' => 1,
                'tags'  => 'lorem,ipsum,dolor'
            ),
            'batch_edit_hash' => $hash->getHash()
        );

        $this->_makePost($post);

        $this->dispatch('/items/batch-edit-save');

        foreach ($itemIds as $id) {
            $item = $this->db->getTable('Item')->find($id);
            $this->assertEquals($item->item_type_id, '1');
            $this->assertEquals(3, count($item->getTags()));
        }

        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        $this->assertContains("The items were successfully changed!", $messages['success']);
    }

    public function testBatchEditContributorUserSaveDisallowedData()
    {
        $this->_authenticateUser($this->_users['contributor']);

        $itemIds = array();

        foreach ($this->_items as $item) {
            $itemIds[] = $item->id;
        }
        
        $this->_makePost();

        $this->dispatch('/items/batch-edit-save');
        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        $this->assertContains("User is not allowed", $messages['error'][0]);
    }
    
    /**
     * @dataProvider userRoleCanDeleteProvider
     */
    public function testBatchDeletePermissions($userRole, $succeeds)
    {
        $hash = new Zend_Form_Element_Hash('batch_edit_hash');
        $hash->initCsrfToken();
        
        $itemIds = array();

        foreach ($this->_items as $item) {
            $itemIds[] = $item->id;
        }
        
        $post = array(
            'items' => $itemIds,
            'delete'  => 1,
            'batch_edit_hash' => $hash->getHash()
        );
        
        $this->_authenticateUser($this->_users[$userRole]);
        $this->_makePost($post);
        $this->dispatch('/items/batch-edit-save');
        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        
        if ($succeeds) {
            $this->assertContains("The items were successfully deleted!", $messages['success']);
            foreach ($itemIds as $id) {
                $item = $this->db->getTable('Item')->find($id);
                $this->assertNull($item);
            }
        } else {
            $this->assertContains("User is not allowed to delete selected items.", $messages['error']);
            foreach ($itemIds as $id) {
                $item = $this->db->getTable('Item')->find($id);
                $this->assertNotNull($item);
            }
        }
    }

    public function testBatchEditMakeNotPublicNotFeatured()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $hash = new Zend_Form_Element_Hash('batch_edit_hash');
        $hash->initCsrfToken();

        $itemIds = array();

        foreach ($this->_items as $item) {
            $itemIds[] = $item->id;
        }

        $post = array(
            'items' => $itemIds,
            'metadata'  => array(
                'public'        => 0,
                'featured'      => 0
            ),
            'batch_edit_hash' => $hash->getHash()
        );

        $this->_makePost($post);
        $this->dispatch('/items/batch-edit-save');
        $this->assertController('items');
        $this->assertAction('batch-edit-save');

        foreach ($itemIds as $id) {
            $item = $this->db->getTable('Item')->find($id);
            $this->assertFalse($item->isPublic());
            $this->assertFalse($item->isFeatured());
        }

        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        $this->assertContains("The items were successfully changed!", $messages['success']);
    }

    public function testBatchEditRemoveMetadata()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $hash = new Zend_Form_Element_Hash('batch_edit_hash');
        $hash->initCsrfToken();

        $itemIds = array();

        foreach ($this->_items as $item) {
            $itemIds[] = $item->id;
        }

        $post = array(
            'items' => $itemIds,
            'metadata'  => array(
                'item_type_id'  => '100',
                'collection_id'    => '100'
            ),
            'removeMetadata' => array(
                'item_type_id'  => '1',
                'collection_id' => '1'
            ),
            'batch_edit_hash' => $hash->getHash()
        );

        $this->_makePost($post);
        $this->dispatch('/items/batch-edit-save');
        $this->assertController('items');
        $this->assertAction('batch-edit-save');

        foreach ($itemIds as $id) {
            $item = $this->db->getTable('Item')->find($id);
            $this->assertNull($item->collection_id);
            $this->assertNull($item->item_type_id);
        }

        $this->assertRedirectTo('/items/browse');
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages = $flash->getCurrentMessages();
        $this->assertContains("The items were successfully changed!", $messages['success']);
    }

    private function _createItem($userRole, $metadata = array())
    {
        $this->_authenticateUser($this->_users[$userRole]);
        $item = new Item;
        $item->public = 1;
        $item->save();
        return $item;
    }

    protected function _makePost($post = null)
    {
        $this->request->setMethod('POST');

        if (!$post) {
            $hash = new Zend_Form_Element_Hash('batch_edit_hash');
            $hash->initCsrfToken();
            $itemIds = array();

            foreach ($this->_items as $item) {
                $itemIds[] = $item->id;
            }
            
            $post = array(
                'items' => $itemIds,
                'metadata'  => array(
                    'public' => 1,
                    'featured' => 1,
                    'item_type_id' => 1,
                    'tags'  => 'lorem,ipsum,dolor'
                ),
                'batch_edit_hash' => $hash->getHash()
            );
        }
        $this->request->setPost($post);
    }
}

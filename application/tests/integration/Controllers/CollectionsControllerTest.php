<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_CollectionsControllerTest extends Omeka_Test_AppTestCase
{   
    public function testRenderAddForm()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $this->dispatch('collections/add');
        $this->assertController('collections');
        $this->assertAction('add');
        $this->assertQuery("input#name");
        self::dbChanged(false);
    }
    
    public function testOwnerIdSetForNewCollections()
    {
        $user = $this->_getDefaultUser();
        $this->_authenticateUser($user);
        $this->request->setPost(array(
            'name' => 'foobar',
            'description' => 'baz'
        ));
        $this->request->setMethod('post');
        $this->dispatch('collections/add');
        $this->assertRedirect();
        $collections = $this->db->getTable('Collection')->findAll();
        $this->assertEquals(1, count($collections));
        $this->assertThat($collections[0], $this->isInstanceOf('Collection'));
        $this->assertNotEquals(0, $collections[0]->owner_id,
            "The collection's owner_id should have been set when saving the form.");
    }
    
    public function testOwnerIdNotSetWhenUpdatingCollection()
    {
        $user = $this->_getDefaultUser();
        $this->_authenticateUser($user);
        $collection = new Collection;
        $collection->name = 'foobar';
        $collection->owner_id = 5;
        $collection->forceSave();
        $this->request->setPost(array(
            'name' => 'foobar',
            'description' => 'baz'
        ));
        $this->request->setMethod('post');
        $this->dispatch('collections/edit/' . $collection->id);
        $this->assertRedirect();
        $updatedCollection = $this->db->getTable('Collection')->find($collection->id);
        $this->assertNotEquals($user->id, $updatedCollection->owner_id,
            "The owner_id for the collection should not be that of the user who updated the collection.");
    }
}

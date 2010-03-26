<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Set of functional tests for checking to see if ACL access for a set of actions if 
 * a user is not logged in.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Controllers_AclTest extends Omeka_Test_AppTestCase
{
    public function testUserIsNotLoggedIn()
    {
        $this->assertNull($this->core->getResource('CurrentUser'));
    }
    
    public function testCanBrowseItems()
    {
        $this->dispatch('items', true);
        $this->assertController('items');
        $this->assertAction('browse');
    }
    
    /**
     * "Access forbidden" is equivalent to being redirected to the login form.
     */
    protected function assertAccessForbidden()
    {
        $this->assertController('users');
        $this->assertAction('login');        
    }
    
    public function testCannotBrowseElementSets()
    {
        $this->dispatch('element-sets', true);
        $this->assertAccessForbidden();
    }
    
    public function testCannotAccessSettingsPage()
    {
        $this->dispatch('settings', true);
        $this->assertAccessForbidden();
    }
    
    public function testCannotAddItems()
    {     
        $this->dispatch('items/add', true);
        $this->assertAccessForbidden();     
    }
    
    public function testCannotAddCollections()
    {
        $this->dispatch('collections/add', true);
        $this->assertAccessForbidden();        
    }
    
    public function testCannotAddItemTypes()
    {
        $this->dispatch('item-types/add', true);
        $this->assertAccessForbidden();        
    }
    
    public function testCannotRemoveCollectorFromCollection()
    {
        $this->dispatch('collections/remove-collector', true);
        $this->assertAccessForbidden();                
    }    
}

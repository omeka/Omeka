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
class Controllers_AclTest extends Omeka_Controller_TestCase
{    
    public function setUpBootstrap($bootstrap)
    {
        $this->_configPublicThemeBootstrap($bootstrap);
    }
    
    public function testUserIsNotLoggedIn()
    {
        $this->assertNull($this->core->getResource('CurrentUser'));
    }
    
    public function testCanBrowseItems()
    {
        $this->dispatch('items');
        $this->assertController('items');
        $this->assertAction('browse');
    }
    
    protected function assertAccessForbidden()
    {
        $this->assertController('error');
        $this->assertAction('forbidden');        
    }
    
    public function testCannotBrowseElementSets()
    {
        $this->dispatch('element-sets');
        $this->assertAccessForbidden();
    }
    
    public function testCannotAccessSettingsPage()
    {
        $this->dispatch('settings');
        $this->assertAccessForbidden();
    }
    
    public function testCannotAddItems()
    {     
        $this->dispatch('items/add');
        $this->assertAccessForbidden();     
    }
    
    public function testCannotAddCollections()
    {
        $this->dispatch('collections/add');
        $this->assertAccessForbidden();        
    }
    
    public function testCannotAddItemTypes()
    {
        $this->dispatch('item-types/add');
        $this->assertAccessForbidden();        
    }
    
    public function testCannotRemoveCollectorFromCollection()
    {
        $this->dispatch('collections/remove-collector');
        $this->assertAccessForbidden();                
    }
    
    public function testCannotUpgradeOmeka()
    {
        $this->dispatch('upgrade');
        $this->assertAccessForbidden();
    }
    
    public function testCannotSeeUpgradedNotice()
    {
        $this->dispatch('upgrade/completed');
        $this->assertAccessForbidden();
    }
}
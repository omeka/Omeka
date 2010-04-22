<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Core_OmekaCoreResourceHtmlpuriferTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function setUp()
    {
        parent::setUp();
        
        // Set the ACL to allow access to collections
        $this->acl = $this->core->getBootstrap()->acl;
        $this->acl->allow(null, 'Collections');
        
        $this->db = $this->core->getBootstrap()->db;
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);
        
        $collection = new Collection();
        $collection->name = 'a';
        $collection->description = 'a';
        $collection->public = true;
        $collection->save();
        
        $this->collection = $collection;
    }
    
    public function assertPreConditions()
    {
        $this->assertTrue($this->collection->exists());
        $this->assertTrue($this->acl->isAllowed($this->user, 'Users', 'edit'));
    }
    
    public function testHtmlPurifyCollectionFormWithAllowedTagInDescription()
    {   
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = '<p>Bob</p>';
        
        $post = $this->collection->toArray();
        $post['description'] = $dirtyHtml;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);
        $this->dispatch('/collections/edit/' . $this->collection->id, true);
                
        $collectionAfter = $this->db->getTable('Collection')->find($this->collection->id);
        $this->assertEquals($cleanHtml, $collectionAfter->description);
    }
    
    public function testHtmlPurifyCollectionFormWithUnallowedTagInDescription()
    {
        $dirtyHtml = '<j>Bob</j>';
        $cleanHtml = 'Bob';
        
        $post = $this->collection->toArray();
        $post['description'] = $dirtyHtml;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);
        $this->dispatch('/collections/edit/' . $this->collection->id, true);
                
        $collectionAfter = $this->db->getTable('Collection')->find($this->collection->id);
        $this->assertEquals($cleanHtml, $collectionAfter->description);
    }
}
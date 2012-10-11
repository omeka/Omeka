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
class Omeka_Controllers_HtmlPurifierTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function setUp()
    {
        parent::setUp();
        
        // Set the ACL to allow access to collections
        $this->acl = $this->application->getBootstrap()->acl;
        $this->acl->allow(null, 'Collections');
        
        $this->db = $this->application->getBootstrap()->db;
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);
        
        // Create a collection
        $collection = new Collection();
        
        $collection->addElementTextsByArray(array(
            'Dublin Core' => array(
                'Title' => array(array('text' => 'a', 'html' => false)),
                'Description' => array(array('text' => 'a', 'html' => false)),
            )
        ));
        
        $collection->collectors = 'a';
        $collection->public = true;
        $collection->save();
        
        $this->collection = $collection;
    }
    
    public function assertPreConditions()
    {
        $this->assertTrue($this->collection->exists());
        $this->assertTrue($this->acl->isAllowed($this->user, 'Collections', 'edit'));
        
        $this->assertEquals(get_option('html_purifier_is_enabled'), '1');
        $this->assertEquals(get_option('html_purifier_allowed_html_elements'), implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertEquals(get_option('html_purifier_allowed_html_attributes'), implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
    }
    
    public function testHtmlPurifyCollectionFormWithAllowedElementAndAllowedAttributeInDescription()
    {
        $this->assertTrue(in_array('p', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertTrue(in_array('*.class', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
           
        $dirtyHtml = '<p class="person">Bob</p>';
        $cleanHtml = '<p class="person">Bob</p>';
        
        $post = $this->collection->toArray();
        $post['description'] = $dirtyHtml;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);
        $this->dispatch('/collections/edit/' . $this->collection->id);
                
        $collectionAfter = $this->db->getTable('Collection')->find($this->collection->id);
        $this->assertEquals($cleanHtml, $collectionAfter->description);
    }
    
    public function testHtmlPurifyCollectionFormWithAllowedElementAndUnallowedAttributeInDescription()
    {
        $this->assertTrue(in_array('p', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertFalse(in_array('*.id', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
           
        $dirtyHtml = '<p id="person">Bob</p>';
        $cleanHtml = '<p>Bob</p>';
        
        $post = $this->collection->toArray();
        $post['description'] = $dirtyHtml;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);
        $this->dispatch('/collections/edit/' . $this->collection->id);
                
        $collectionAfter = $this->db->getTable('Collection')->find($this->collection->id);
        $this->assertEquals($cleanHtml, $collectionAfter->description);
    }
    
    public function testHtmlPurifyCollectionFormWithUnallowedElementAndAllowedAttributeInDescription()
    {
        $this->assertFalse(in_array('j', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertTrue(in_array('*.class', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
        
        $dirtyHtml = 'Bob is <j class="trait">bad</j>.';
        $cleanHtml = 'Bob is bad.';
        
        $post = $this->collection->toArray();
        $post['description'] = $dirtyHtml;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);
        $this->dispatch('/collections/edit/' . $this->collection->id);
                
        $collectionAfter = $this->db->getTable('Collection')->find($this->collection->id);
        $this->assertEquals($cleanHtml, $collectionAfter->description);
    }
    
    public function testHtmlPurifyCollectionFormWithUnallowedElementAndUnallowedAttributeInDescription()
    {
        $this->assertFalse(in_array('j', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertFalse(in_array('*.id', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
        
        $dirtyHtml = '<j id="person">Bob</j> is bad.';
        $cleanHtml = 'Bob is bad.';
        
        $post = $this->collection->toArray();
        $post['description'] = $dirtyHtml;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);
        $this->dispatch('/collections/edit/' . $this->collection->id);
                
        $collectionAfter = $this->db->getTable('Collection')->find($this->collection->id);
        $this->assertEquals($cleanHtml, $collectionAfter->description);
    }
    
    public function testHtmlPurifyCollectionFormWithAllowedAndUnallowedElementsAndAttributesInDescription()
    {
        $this->assertTrue(in_array('strong', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertTrue(in_array('p', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertFalse(in_array('j', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertTrue(in_array('*.class', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
        $this->assertFalse(in_array('*.id', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
        
        $dirtyHtml = '<p class="person" id="person">Bob is bad <j>and mean<j> and <strong id="trait">fun</strong>.</p>';
        $cleanHtml = '<p class="person">Bob is bad and mean and <strong>fun</strong>.</p>';
        
        $post = $this->collection->toArray();
        $post['description'] = $dirtyHtml;
        
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost($post);
        $this->dispatch('/collections/edit/' . $this->collection->id);
                
        $collectionAfter = $this->db->getTable('Collection')->find($this->collection->id);
        $this->assertEquals($cleanHtml, $collectionAfter->description);
    }
}

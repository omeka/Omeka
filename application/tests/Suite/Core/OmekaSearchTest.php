<?php
class Core_OmekaSearchTest extends Omeka_Model_TestCase
{		
	protected $_search;
	
	public function setUpBootstrap($bootstrap)
    {
        $bootstrap->registerPluginResource('Search');
        $bootstrap->bootstrap('Search');
        parent::setUpBootstrap($bootstrap);
    }
	
	public function setUp()
	{
	    parent::setUp();
	    $this->_search = $this->core->getResource('Search');
	}
	
	public function testGetLuceneIndex()
	{
        $this->assertNotNull($this->_search->getLuceneIndex());
    }
    
    public function testCreateLuceneFieldNameWithSingleStringSingleValue()
    {
        $fieldNameStrings = 'Title';
        $this->assertEquals('title', Omeka_Search::createLuceneFieldName($fieldNameStrings));
    }
	
	public function testCreateLuceneFieldNameWithSingleStringMultipleValues()
    {
        $fieldNameStrings = 'Title';
        $fieldValueNumber = 1;
        $this->assertEquals('title__1', Omeka_Search::createLuceneFieldName($fieldNameStrings, 1));
    }
	
	public function testCreateLuceneFieldNameWithMultipleStringsSingleValue()
    {
        $fieldNameStrings = array('Title', 'Author', 'Description');
        $this->assertEquals('title__author__description', Omeka_Search::createLuceneFieldName($fieldNameStrings));
    }
	
	public function testCreateLuceneFieldNameWithMultipleStringsMultipleValues()
    {
        $fieldNameStrings = array('Title', 'Author', 'Description');
        $fieldValueNumber = 1;
        $this->assertEquals('title__author__description__1', Omeka_Search::createLuceneFieldName($fieldNameStrings, 1));
    }
	
	public function tearDown()
    {
        $this->_search->deleteLuceneIndex();
        parent::tearDown();
    }
}
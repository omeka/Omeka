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
    
    public function testGetLuceneExpandedFieldNameWithSingleStringSingleValue()
    {
        $fieldNameStrings = 'Title';
        $this->assertEquals('title@1', $this->_search->getLuceneExpandedFieldName($fieldNameStrings));
    }
	
	public function testGetLuceneExpandedFieldNameWithSingleStringMultipleValues()
    {
        $fieldNameStrings = 'Title';
        $fieldValueNumber = 1;
        $this->assertEquals('title@1', $this->_search->getLuceneExpandedFieldName($fieldNameStrings, $fieldValueNumber));
    }
	
	public function testGetLuceneExpandedFieldNameWithMultipleStringsSingleValue()
    {
        $fieldNameStrings = array('Title', 'Author', 'Description');
        $this->assertEquals('title.author.description@1', $this->_search->getLuceneExpandedFieldName($fieldNameStrings));
    }
	
	public function testGetLuceneExpandedFieldNameWithMultipleStringsMultipleValues()
    {
        $fieldNameStrings = array('Title', 'Author', 'Description');
        $fieldValueNumber = 1;
        $this->assertEquals('title.author.description@1', $this->_search->getLuceneExpandedFieldName($fieldNameStrings, $fieldValueNumber));
    }
	
	public function testGetLuceneUnexpandedFieldNameWithSingleString()
    {
        $fieldNameStrings = 'Title';
        $this->assertEquals('title', $this->_search->getLuceneUnexpandedFieldName($fieldNameStrings));
    }
	
	public function testGetLuceneUnexpandedFieldNameWithMultipleStrings()
    {
        $fieldNameStrings = array('Title', 'Author', 'Description');
        $this->assertEquals('title.author.description', $this->_search->getLuceneUnexpandedFieldName($fieldNameStrings));
    }
	
	public function testAddLuceneKeywordField()
	{
	    $fieldName = 'Title';
        $doc = new Zend_Search_Lucene_Document(); 
        Omeka_Search::addLuceneField($doc, 'Keyword', $fieldName , array('A', 'B', 'C'));
        
        // make sure the field names are correct
        $fieldNames = $doc->getFieldNames();
        $this->assertEquals(array('title@1', 'title@2', 'title@3'), $fieldNames);
        
        // make sure the fields have the values
        $this->assertEquals('A', $doc->getFieldValue('title@1'));
        $this->assertEquals('B', $doc->getFieldValue('title@2'));
        $this->assertEquals('C', $doc->getFieldValue('title@3'));
        
        // make sure all of the fields are keyword fields
        foreach($fieldNames as $fieldName) {
            $field = $doc->getField($fieldName);
            $this->assertTrue($field->isStored);
            $this->assertTrue($field->isIndexed);
            $this->assertFalse($field->isTokenized);
        }  
	}
	
    public function testGetLuceneExpandedFieldNames()
    {
        // create a lucene document with a single multi-value field
        $fieldName = 'Title';
        $doc = new Zend_Search_Lucene_Document(); 
        Omeka_Search::addLuceneField($doc, 'Keyword', $fieldName , array('A', 'B', 'C'));
    
        // add the lucene document to the lucene index
        $luceneIndex = $this->_search->getLuceneIndex(); 
        $luceneIndex->addDocument($doc);
    
        $expandedFieldNames = $this->_search->getLuceneExpandedFieldNames($this->_search->getLuceneUnexpandedFieldName('Title'));
        $this->assertEquals(array('title@1', 'title@2', 'title@3'), $expandedFieldNames);
    }
    
    public function testGetLuceneQueryForFieldNameWithOneDoc()
    {
        $fieldName = 'Title';
        $index = $this->_search->getLuceneIndex();

        $doc = new Zend_Search_Lucene_Document(); 
        Omeka_Search::addLuceneField($doc, 'Keyword', $fieldName , array('Andy', 'Bob Jones', 'Chris Smith'));
        $index->addDocument($doc);
                
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $subquery = Omeka_Search::getLuceneQueryForFieldName($fieldName, 'Andy');                
        $query->addSubquery($subquery);                            
        $hits = $index->find($query);
        $this->assertEquals(1, count($hits));
        
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $subquery = Omeka_Search::getLuceneQueryForFieldName($fieldName, 'Bob');                
        $query->addSubquery($subquery);                            
        $hits = $index->find($query);
        $this->assertEquals(0, count($hits));
        
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $subquery = Omeka_Search::getLuceneQueryForFieldName($fieldName, 'Chris Smith');                
        $query->addSubquery($subquery);                            
        $hits = $index->find($query);
        $this->assertEquals(1, count($hits));
    }
    
    public function testGetLuceneQueryForFieldNameWithMultipleDocs()
    {
        $fieldName = 'Title';
        $index = $this->_search->getLuceneIndex();
        
        $maxDocs = 50;
        for($i = 0; $i < $maxDocs; $i++) {
            $doc = new Zend_Search_Lucene_Document(); 
            Omeka_Search::addLuceneField($doc, 'Keyword', $fieldName , array('Andy', 'Bob Jones', 'Chris Smith'));
            $index->addDocument($doc);
        }
      
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $subquery = Omeka_Search::getLuceneQueryForFieldName($fieldName, 'Andy');                
        $query->addSubquery($subquery);                            
        $hits = $index->find($query);
        $this->assertEquals($maxDocs, count($hits));
        
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $subquery = Omeka_Search::getLuceneQueryForFieldName($fieldName, 'Bob');                
        $query->addSubquery($subquery);                            
        $hits = $index->find($query);
        $this->assertEquals(0, count($hits));
        
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $subquery = Omeka_Search::getLuceneQueryForFieldName($fieldName, 'Chris Smith');                
        $query->addSubquery($subquery);                            
        $hits = $index->find($query);
        $this->assertEquals($maxDocs, count($hits));
    }
		
	public function tearDown()
    {
        $this->_search->deleteLuceneIndex();
        parent::tearDown();
    }
}
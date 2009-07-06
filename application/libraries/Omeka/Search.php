<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * A wrapper for Zend_Search_Lucene
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Search
{
    static private $_instance;
    
    private $_luceneIndex;
    
    /**
     * Gets the single instance of Omeka_Search used by Omeka
     *
     * @return Omeka_Search
     **/
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Constructs an instance of Omeka_Search
     *
     **/
    public function __construct()
    {
        // Open the Lucene index if one already exists, otherwise create a new one.
        try {
            $this->_luceneIndex = Zend_Search_Lucene::open(LUCENE_INDEX_DIR);
        } catch (Exception $e) {
            $this->_luceneIndex = Zend_Search_Lucene::create(LUCENE_INDEX_DIR);
        }
    }
    
    /**
     * Gets the single Lucene index object used by Omeka.
     *
     * @return Zend_Search_Lucene_Document
     **/
    public function getLuceneIndex()
    {
        return $this->_luceneIndex;
    }
    
    /**
     * Updates the Lucene index with the Zend_Search_Lucene_Document of an Omeka_Record
     * If Lucene index already has a Lucene document for the record, 
     * it deletes the lucene document from the index and adds a new one.
     *
     * @param Omeka_Record $record The Omeka_Record to index.
     * @return void
     **/
    public function updateLucene($record)
    {
        // create a lucene document for the record
        $doc = $record->createLuceneDocument();
        
        if ($doc) {
            
            // delete the document from the index if it already exists
            $this->deleteLucene($record);
            
            // add the document to the index
            $this->_luceneIndex->addDocument($doc);
        }
    }
    
    /**
     * Deletes the Zend_Search_Lucene_Document of an Omeka_Record
     * from the Lucene index.
     *
     * @param Omeka_Record $record The Omeka_Record to delete from the index.
     * @return void
     **/
    public function deleteLucene($record)
    {
        // delete the document from the index if it already exists
        if ($hit = $this->findByRecordLucene($record)) {
            $this->_luceneIndex->delete($hit->id);
        }
    }
    
    /**
     * Returns the Zend_Search_Lucene_Search_QueryHit object
     * associated for an Omeka_Record if one exists, otherwise returns null.
     *
     * @param Omeka_Record $record
     * @return Zend_Search_Lucene_Search_QueryHit
     */
    public function findByRecordLucene($record) 
    {
        // create a query to find the queryhit associated with the Omeka_Record
        $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
        $query->addTerm(new Zend_Search_Lucene_Index_Term(get_class($record), 'model_name', true));
        $query->addTerm(new Zend_Search_Lucene_Index_Term($record->id, 'model_id', true));
        
        // return a single hit if one exists, otherwise return null
        $hits  = $this->_luceneIndex->find($query);        
        if (!empty($hits)) {
            return $hits[0];
        }
        return null;
    }
    
    /**
     * Returns a lucene field name based on an arbitrary number of string arguments.
     * The function takes an arbitrary list of string parameters, and the returned string is underscored
     * concatenated in the order of the input arguments.
     * 
     * @param fieldNameStrings string|array The string or array of strings to concatenate to form the field name. 
     * Note: The order of the strings matters.
     * @param $fieldNameNumber int The index number of the field value, starting at 1.  
     * This is used for nameing field names for fields with multiple values.
     * For example, suppose you have 3 values for the Title field.  
     * Then your field names would be: Title_1, Title_2, Title_3
     * @return string
     */
    static public function createLuceneFieldName($fieldNameStrings, $fieldValueNumber=0)
    {
        // if the fieldNameStrings is just a single string, then put it into an array
        if (is_string($fieldNameStrings)) {
            $fieldNameStrings = array($fieldNameStrings);
        }
        
        // underscore the field name strings
        $s = array();
        foreach($fieldNameStrings as $fieldNameString) {
            $s[] = Inflector::underscore($fieldNameString);
        }
        
        // add the field value number if applicable
        if (is_numeric($fieldValueNumber)) {
            $fieldValueNumber = (int)$fieldValueNumber;
            if ($fieldValueNumber != 0) {
                $s[] = ($fieldValueNumber . '');
            }
        }
        
        // concatenate the field name strings and field value number, and then return the field name
        return implode('__', $s);
    }
}
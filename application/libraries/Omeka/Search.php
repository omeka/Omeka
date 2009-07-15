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
    
    const FIELD_NAME_STRING_DELIMITER = '.';
    const FIELD_NAME_VALUE_NUM_DELIMITER = '@';
    const FIELD_NAME_MODEL_NAME = 'model_name';
    const FIELD_NAME_MODEL_ID = 'model_id';
    
    
    private $_luceneIndex;
    private $_luceneIndexDir;
    private $_luceneFieldNameValueCounts;
    
    /**
     * Gets the single instance of Omeka_Search used by Omeka
     *
     * @param $luceneIndexDir The directory path of the lucene index
     * @return Omeka_Search
     **/
    public static function getInstance($luceneIndexDir = LUCENE_INDEX_DIR)
    {
        if (!self::$_instance) {
            self::$_instance = new self($luceneIndexDir);
        }
        return self::$_instance;
    }
    
    /**
     * Constructs an instance of Omeka_Search
     *
     * @param $luceneIndexDir The directory path of the lucene index
     **/
    private function __construct($luceneIndexDir)
    {
        // load the lucene index
        $this->_loadLuceneIndex($luceneIndexDir);
    }
    
    /**
     * Creates and loads the lucene index if it is not already loaded
     *
     * @param $luceneIndexDir The directory path of the lucene index
     * @return void
     **/
    private function _loadLuceneIndex($luceneIndexDir)
    {        
        // Save the directory name of the index
        $this->_luceneIndexDir = $luceneIndexDir;
        
        // Set the field name value count array
        $this->_luceneFieldNameValueCounts = array();      
        
        // Open the Lucene index if one already exists, otherwise create a new one.
        try {
            $this->_luceneIndex = Zend_Search_Lucene::open($this->_luceneIndexDir);
        } catch (Exception $e) {
            $this->_luceneIndex = Zend_Search_Lucene::create($this->_luceneIndexDir);
        }
        // compute the maximum number of field values for each field name
        $this->updateLuceneFieldNameValueCounts();
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
     * Deletes the single Lucene index object used by Omeka.
     *
     * @return Zend_Search_Lucene_Document
     **/
    public function deleteLuceneIndex()
    {
        $this->_luceneIndex = null;        
        $this->_unlinkDirectory($this->_luceneIndexDir);
        self::$_instance = null;
        unset($this);
    }

    /**
     * Recursively deletes a directory
     *
     * @param string $dirname The name of the directory to delete recursively
     * @return void
     **/
     private function _unlinkDirectory($dirname) 
     {         
         if (!is_dir($dirname)) {
             trigger_error('Given argument missing or not a directory. ' . $dirname, E_USER_ERROR);
         } else {
             $dir = opendir($dirname);
             while(($filename = readdir($dir)) !== false) {
                 $path = $dirname . DIRECTORY_SEPARATOR . $filename;
                 if (is_dir($path)) {
                     if ($filename == '.' || $filename == '..') {
                         continue;        
                     }
                     $this->_unlinkDirectory($path);
                 } else {
                     unlink($path);
                 }
             }
         }
         closedir($dir);         
         rmdir($dirname);         
    }
    
    /**
     * Updates the Lucene index with the Zend_Search_Lucene_Document of an Omeka_Record
     * If Lucene index already has a Lucene document for the record, 
     * it deletes the lucene document from the index and adds a new one.
     *
     * @param Omeka_Record $record The Omeka_Record to index.
     * @return void
     **/
    public function updateLuceneByRecord($record)
    {        
        // create a lucene document for the record
        $doc = $record->createLuceneDocument();
                        
        if ($doc) {
            // delete the document from the index if it already exists
            $this->deleteLuceneByRecord($record);
            
            // add the document to the index
            $this->_luceneIndex->addDocument($doc);
            $this->_luceneIndex->commit();
        }
    }
    
    /**
     * Deletes the Zend_Search_Lucene_Document of an Omeka_Record
     * from the Lucene index.
     *
     * @param Omeka_Record $record The Omeka_Record to delete from the index.
     * @return void
     **/
    public function deleteLuceneByRecord($record)
    {        
        // delete the document from the index if it already exists
        if ($hit = $this->findLuceneByRecord($record)) {
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
    public function findLuceneByRecord($record) 
    {
        // create a query to find the queryhit associated with the Omeka_Record
        $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
        $query->addTerm(new Zend_Search_Lucene_Index_Term(get_class($record), $this->getLuceneExpandedFieldName('model_name')), true);
        $query->addTerm(new Zend_Search_Lucene_Index_Term($record->id, $this->getLuceneExpandedFieldName('model_id')), true);
                
        // return a single hit if one exists, otherwise return null
        $hits  = $this->_luceneIndex->find($query);        
                
        if (!empty($hits)) {
            return $hits[0];
        }
        return null;
    }
    
    
    /**
     * Updates and stores the maximum number of values for the specified field
     * 
     * @param string $unexpandedFieldName The unexpanded field name to update
     * @param string $fieldNameValueNumber The number of the field value 
     * @return void
     */
    public function updateLuceneFieldNameValueCount($unexpandedFieldName, $fieldNameValueNumber)
    {
        $fieldNameValueNumber = (int) $fieldNameValueNumber;
        
        if (empty($this->_luceneFieldNameValueCounts[$unexpandedFieldName])) {
            $fieldNameValueCount = 0;
        } else {
            $fieldNameValueCount = $this->_luceneFieldNameValueCounts[$unexpandedFieldName];
        }
            
        if ( $fieldNameValueNumber > $fieldNameValueCount) {
            $this->_luceneFieldNameValueCounts[$unexpandedFieldName] = $fieldNameValueNumber;
        }
    }
    
    /**
     * Computes and stores the maximum number of values for each field name based on the field names in the lucene index.
     * 
     * @return void
     */
    public function updateLuceneFieldNameValueCounts() 
    {        
        // determine the maximum number of field values for each field
        // store the maximum number of field values in an associative array, where the key is the field name
        $this->_luceneFieldNameValueCounts = array();
        $fieldNames = $this->_luceneIndex->getFieldNames();
        foreach($fieldNames as $fieldName) {
            $fieldNameParts = explode(self::FIELD_NAME_VALUE_NUM_DELIMITER, $fieldName);
            $fieldNameString = $fieldNameParts[0];
            $fieldNameValueNumber = (int)$fieldNameParts[1];
            $this->updateLuceneFieldNameValueCount($fieldNameString, $fieldNameValueNumber);
        }
    }
    
    /**
     * Returns an array of strings with expanded field names
     * 
     * @param string fieldNameStrings The fieldName strings used to create an unexpanded field name.
     * @param string $fieldValue The required field value for the field.
     * @return Zend_Search_Lucene_Search_Query_Boolean The subquery that includes a disjunction 
     * for all of the variants of the field name.
     */
    static public function getLuceneQueryForFieldName($fieldNameStrings, $fieldValue) 
    {
        $search = self::getInstance();
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $expandedFieldNames = $search->getLuceneExpandedFieldNames($search->getLuceneUnexpandedFieldName($fieldNameStrings));
        foreach($expandedFieldNames as $expandedFieldName) {
            //$subquery = new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($fieldValue, $expandedFieldName));
            // hack
            $subquery = new Zend_Search_Lucene_Search_Query_Preprocessing_Term($fieldValue, 'UTF-8', $expandedFieldName);
            $query->addSubquery($subquery);
        }    
        return $query;
    }
    
    /**
     * Returns an array of strings with expanded field names
     * 
     * @return array An array of strings with expanded field names
     */
    public function getLuceneExpandedFieldNames($unexpandedFieldName) 
    {
        $expandedFieldNames = array();
        $fieldValueCountForFieldName = $this->_luceneFieldNameValueCounts[$unexpandedFieldName];        
        for($i = 1; $i <= $fieldValueCountForFieldName; $i++) {
            $expandedFieldNames[] = $unexpandedFieldName . self::FIELD_NAME_VALUE_NUM_DELIMITER . $i;
        }
        return $expandedFieldNames;
    }
        
    /**
     * Returns a lucene field name based on an arbitrary number of string arguments.
     * The function takes an arbitrary list of string parameters, and the returned string is lowercased, underscored
     * concatenated in the order of the input arguments.
     * 
     * @param fieldNameStrings string|array The string or array of strings to concatenate to form the field name. 
     * Note: The order of the strings matters.
     * @param $fieldNameValueNumber int The number of the field value, starting at 1.  It must be a positive integer.
     * This is used for naming field names for fields with multiple values.
     * For example, suppose you have 3 values for the Title field.  
     * Then your field names would be: title@1, title@2, title@3
     * Also, for example, suppose you have 3 values for Date Added field.
     * Then your field names would be: date.added@1, date.added@2, date.added@3
     * @return string
     */
    public function getLuceneExpandedFieldName($fieldNameStrings, $fieldNameValueNumber=1)
    {
        // get the unexpanded field name
        $unexpandedFieldName = $this->getLuceneUnexpandedFieldName($fieldNameStrings);
        
        // add the field value number if applicable
        if (is_numeric($fieldNameValueNumber) && $fieldNameValueNumber > 0) {
            $fieldValueNumber = (int)$fieldNameValueNumber;
            
            // update the number of values for the field name
            $this->updateLuceneFieldNameValueCount($unexpandedFieldName, $fieldNameValueNumber);
            
            $expandedFieldName = $unexpandedFieldName . self::FIELD_NAME_VALUE_NUM_DELIMITER . $fieldNameValueNumber;
            return $expandedFieldName;
        }
        throw new Exception('Invalid field name value number. It must be a positive integer.');
    }
    
    /**
     * Returns an unexpanded lucene field name based on an arbitrary number of string arguments.
     * The function takes an arbitrary list of string parameters, and then returns a string that is lowercased, underscored
     * concatenates fieldNameStrings in order.
     * For example, if $fieldNameStrings = array('A', 'B', 'C'), then it returns 'A.B.C'
     * 
     * @param string|array $fieldNameStrings The string or array of strings to concatenate to form the field name. 
     * Note: The order of the strings matters.
     * @return string
     */
    public function getLuceneUnexpandedFieldName($fieldNameStrings)
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
        
        // concatenate 
        $unexpandedFieldName = implode(self::FIELD_NAME_STRING_DELIMITER, $s);
        
        return $unexpandedFieldName;
    }
    
    /**
     * Adds a Lucene field to a lucene doc
     * 
     * @param Zend_Search_Lucene_Document $luceneDoc The lucene doc with which to add the field
     * @param string $luceneFieldType The type of lucene field to add: 'Text', 'UnStored', 'Keyword', 'UnIndexed', 'Binary'
     * @param array|string $fieldNameStrings The string or array of strings to concatenate to form the field name. 
     * Note: The order of the strings matters.
     * @param array|string $fieldValues The string values to add to the field.
     * @return void
     */
    static public function addLuceneField($luceneDoc, $luceneFieldType, $fieldNameStrings, $fieldValues) 
    {                
        if (!is_array($fieldValues)) {
            $fieldValues = (string)$fieldValues; // convert numbers to strings
            if (trim($fieldValues) == '') {
                return;
            }
            $fieldValues = array($fieldValues);
        }

        if (count($fieldValues) == 0) {
            return ;
        }
        
        $search = Omeka_Search::getInstance();
        
        $unexpandedFieldName = $search->getLuceneUnexpandedFieldName($fieldNameStrings);
        
        // find the maximum fieldNameValueNumber for the document
        $docFieldNames = $luceneDoc->getFieldNames();
        $maxFieldNameValueNumberForDoc = 0;
        foreach($docFieldNames as $docFieldName) {
            $docFieldNameParts = explode(self::FIELD_NAME_VALUE_NUM_DELIMITER, $fieldName);
            $docUnexpandedFieldName = $fieldNameParts[0];
            $docFieldNameValueNumber = (int)$fieldNameParts[1];
            if ($docUnexpandedFieldName == $unexpandedFieldName) {
               if ($docFieldNameValueNumber > $maxFieldNameValueNumberForDoc) {
                   $maxFieldNameValueNumberForDoc = $docFieldNameValueNumber;
               }
            }
        }
                
        // add the new fields         
        $i = $maxFieldNameValueNumberForDoc + 1; 
        foreach($fieldValues as $fieldValue) {
            // update the number of values for the field name            
            $search->updateLuceneFieldNameValueCount($unexpandedFieldName, $i);
            $expandedFieldName = $unexpandedFieldName . self::FIELD_NAME_VALUE_NUM_DELIMITER . $i;
            $luceneDoc->addField(Zend_Search_Lucene_Field::$luceneFieldType($expandedFieldName, $fieldValue));
            $i++;
        }        
    }
    
    /**
     * Gets the Record object associated with a lucene doc
     * 
     * @param Zend_Search_Lucene_Document $luceneDoc The lucene doc with which to add the field
     * @return void
     */
    public function getRecordByLuceneDocument($luceneDoc) 
    {
        $modelName = $luceneDoc->getFieldValue($this->getLuceneExpandedFieldName(self::FIELD_NAME_MODEL_NAME));
        $modelId = $luceneDoc->getFieldValue($this->getLuceneExpandedFieldName(self::FIELD_NAME_MODEL_ID));
        
        $db = Omeka_Context::getInstance()->getDb();
        $record = $db->getTable($modelName)->find($modelId);
        
        return $record;
    }    
}
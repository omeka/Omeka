<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Build or update an {@link Omeka_Record} as needed.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
abstract class Omeka_Record_Builder
{    
    /**
     * Class of record that the builder will create.
     *
     * @var string
     */
    protected $_recordClass;
    
    /**
     * String names denoting the properties of a specific record
     * that can be set directly through the builder.  This will not always
     * be all of the fields for the record.
     *
     * @var array
     */
    protected $_settableProperties = array();
        
    /**
     * Parsed metadata options for the builder.
     *
     * @var array
     */
    private $_metadataOptions = array();
    
    /**
     * Record being built or updated.
     *
     * @var Omeka_Record
     */
    protected $_record;    
    
    /**
     * @var Omeka_Db
     */
    protected $_db;
    
    public function __construct(Omeka_Db $db)
    {
        $this->_db = $db;
    }
    
    /**
     * Build the actual record.  If the record already exists, update it as 
     * necessary.
     *
     * @return Omeka_Record
     */    
    public function build()
    {
        $record = $this->getRecord();
        $this->_setRecordProperties($record);
        $this->_beforeBuild($record);
        $record->forceSave();
        $this->_afterBuild($record);
        return $record;        
    }
    
    /**
     * Set basic metadata for the record. 
     * 
     * Note that the columns to be set must be specified in the $_settableProperties
     * property of subclassed Builders.
     * 
     * @param array $metadata
     * @return void
     */
    public function setRecordMetadata(array $metadata)
    {
        $this->_metadataOptions = $metadata;
    }
    
    /**
     * Get the metadata that will be saved to the record.
     * 
     * @return array
     */
    public function getRecordMetadata()
    {
        return $this->_metadataOptions;
    }

    /**
     * Get the record that is being acted upon by the builder.
     * 
     * When an Omeka_Record instance has been provided via setRecord(), that will
     * be returned.  If a record ID has been provided, then the appropriate 
     * record will be returned.
     * 
     * Otherwise, a new instance of Omeka_Record will be returned.
     * 
     * @return Omeka_Record
     */
    public function getRecord()
    {        
        if (!($this->_record instanceof Omeka_Record)) {
            $this->setRecord($this->_record);
        }
        return $this->_record;
    }
    
    /**
     * Set the record upon which this builder will act.
     * 
     * @see Omeka_Record_Builder::getRecord()
     * @param Omeka_Record|integer|null $record
     * @return void
     */
    public function setRecord($record = null)
    {
        if ($record === null) {
            $this->_record = new $this->_recordClass($this->_db);        
        } else if ($record instanceof Omeka_Record) {
            if (!($record instanceof $this->_recordClass)) {
                throw new Omeka_Record_Builder_Exception("Incorrect record instance given.  Must be instance of '$this->_recordClass'.");
            }
            $this->_record = $record;
        } else if (is_int($record)) {
            $this->_record = $this->_db->getTable($this->_recordClass)->find($record);
            if (!$this->_record) {
                throw new Omeka_Record_Builder_Exception("Could not find record with ID = " . $record);
            }
        } else {
            throw new InvalidArgumentException("Argument passed to setRecord() must be Omeka_Record, integer, or null.");
        }
    }
    
    /**
     * All necessary tasks to take place before the record is inserted.
     * 
     * Exceptions may be thrown, validation errors may be added.
     *
     * @return void
     */
    protected function _beforeBuild(Omeka_Record $record)
    {}
    
    /**
     * All necessary tasks that take place after the record has been inserted
     * into the database.  
     * 
     * Should not throw exceptions in this method.
     *
     * @return void
     */
    protected function _afterBuild(Omeka_Record $record)
    {}
        
    /**
     * Set the properties for the record, taking care to filter based on the 
     * $_settableProperties array.
     * 
     * @param Omeka_Record $record
     * @return void
     */
    private function _setRecordProperties($record)
    {
        $properties = array_intersect_key($this->getRecordMetadata(), array_flip($this->_settableProperties));
        $record->setArray($properties);
    }
}

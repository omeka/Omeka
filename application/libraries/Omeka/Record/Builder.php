<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Build or update an {@link Omeka_Record} as needed.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009-2010
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
    protected $_metadataOptions = array();
    
    /**
     * Record being built or updated.
     *
     * @var Omeka_Record
     */
    protected $_record;    
    
    /**
     * @param array $metadata Metadata options for the builder subclass.
     * @param integer|Omeka_Record|null $record (optional) An Omeka_Record
     * instance (or id) to update, rather than creating a new one.
     */
    public function __construct($metadata = array(), $record = null)
    {
        $this->_record = $this->_findOrBuildRecord($record);
        $this->_metadataOptions = $this->_parseMetadataOptions($metadata);
    }
    
    /**
     * Build the actual record.  If the record already exists, update it as 
     * necessary.
     *
     * @return Omeka_Record
     */    
    public function build()
    {
        $this->_setRecordProperties();
        $this->_beforeBuild();
        $this->_record->forceSave();
        $this->_afterBuild();        
        return $this->_record;
    }
    
    /**
     * All necessary tasks to take place before the record has been inserted.
     * 
     * Exceptions may be thrown, validation errors may be added.
     *
     * @return void
     */
    protected function _beforeBuild()
    {}
    
    /**
     * All necessary tasks that take place after the record has been inserted
     * into the database.  
     * 
     * Should not throw exceptions in this method.
     *
     * @return void
     */
    protected function _afterBuild()
    {}
    
    /**
     * All metadata properties for the record should be in the top level of the
     * array.
     *
     * @return void
     */
    private function _setRecordProperties()
    {
        foreach ($this->_settableProperties as $propName) {
            if (array_key_exists($propName, $this->_metadataOptions)) {
                $this->_record->$propName = $this->_metadataOptions[$propName];
            }
        }
    }
    
    /**
     * May be overridden by subclasses to clean up the input instructions.
     * 
     * Throw exceptions here to indicate invalid arguments provided.
     *
     * @param array $metadata
     * @return array
     */
    protected function _parseMetadataOptions(array $metadata)
    {
        return $metadata;
    }
    
    /**
     * Create a new record instance or retrieve an existing instance from the 
     * database.
     *
     * @param integer|Omeka_Record|null $record
     * @return Omeka_Record The source of the returned record varies depending
     * on the type of the $record argument passed:
     * - Omeka_Record: Return the passed $record directly.
     * - integer: Look up the record with the specified ID in the database.
     * - null or invalid: Create a new record.
     */
    private function _findOrBuildRecord($record)
    {
        if ($record instanceof Omeka_Record) {
            return $record;
        } else if (is_int($record)){
            $recordObj = get_db()->getTable($this->_recordClass)->find($record);
            if (!$recordObj) {
                throw new Omeka_Record_Builder_Exception("Could not find record with ID=" . $record);
            }
            return $recordObj;
        } else {
            return new $this->_recordClass;
        }
    }
}

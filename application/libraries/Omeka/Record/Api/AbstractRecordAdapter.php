<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Record\Api
 */
abstract class Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST representation of a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     */
    abstract public function getRepresentation(Omeka_Record_AbstractRecord $record);
    
    /**
     * Set data to a record.
     * 
     * @param Omeka_Record_AbstractRecord $data
     * @param mixed $data
     */
    abstract public function setData(Omeka_Record_AbstractRecord $record, $data);
}

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
interface Omeka_Record_Api_RecordAdapterInterface
{
    /**
     * Get the REST representation of a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record);
    
    /**
     * Set data to a record during a POST request.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data);
    
    /**
     * Set data to a record during a PUT request.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data);
}

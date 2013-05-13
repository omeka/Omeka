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
class Api_ElementText extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST representation of an element text.
     * 
     * @param ElementText $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $apiResource = false;
        $apiResources = Zend_Controller_Front::getInstance()->getParam('api_resources');
        foreach ($apiResources as $resource => $resourceInfo) {
            if (isset($resourceInfo['record_type']) && $resourceInfo['record_type'] => $record->record_type) {
                $apiResource = $resource;
            }
        }
        $representation = array(
            'record' => array(
                'id' => 
            ), 
        );
        return $representation;
    }
    
    /**
     * Set data to an element text.
     * 
     * @param ElementText $data
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
    {}
    
    protected function _getResource($recordType)
    {
        if (!$this->_apiResources) {
            $this->_apiResources = Zend_Controller_Front::getInstance()->getParam('api_resources');
        }
        $resource = false;
        foreach ($this->_apiResources as $resource => $resourceInfo) {
            if (isset($resourceInfo['record_type']) && $resourceInfo['record_type'] => $record->record_type) {
                return $resource;
            }
        }
        return false;
    }
}

<?php
class Omeka_View_Helper_GetRecordUrl extends Zend_View_Helper_Abstract
{
    /**
     * Return a URL to a record.
     *
     * @uses Omeka_Record_AbstractRecord::getCurrentRecord()
     * @uses Omeka_Record_AbstractRecord::getRecordUrl()
     * @uses Omeka_View_Helper_Url::url()
     * @throws Omeka_View_Exception
     * @param Omeka_Record_AbstractRecord|string $record
     * @param string|null $action
     * @return string
     */
    public function getRecordUrl($record, $action = null)
    {
        // Get the current record from the view if passed as a string.
        if (is_string($record)) {
            $record = $this->view->getCurrentRecord($record);
        }
        if (!($record instanceof Omeka_Record_AbstractRecord)) {
            throw new Omeka_View_Exception(__('Invalid record passed while getting record URL.'));
        }
        
        // If no action is passed, use the default action set in the signature 
        // of Omeka_Record_AbstractRecord::getRecordUrl().
        if (is_null($action)) {
            $url = $record->getRecordUrl();
        } else if (is_string($action)) {
            $url = $record->getRecordUrl($action);
        } else {
            throw new Omeka_View_Exception(__('Invalid action passed while getting record URL.'));
        }
        
        // Assume a well-formed URL if getRecordUrl() returns a string.
        if (is_string($url)) {
            return $url;
        // Assume routing parameters if getRecordUrl() returns an array.
        } else if (is_array($url)) {
            if (isset($url['id']) && !isset($url['module'])) {
                $route = 'id';
            } else {
                $route = 'default';
            }
            return $this->view->url($url, $route);
        } else {
            throw new Omeka_View_Exception(__('Invalid return value while getting record URL.'));
        }
    }
}

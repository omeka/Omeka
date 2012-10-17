<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_SetCurrentRecord extends Zend_View_Helper_Abstract
{
    /**
     * Set a record to the view as the current record.
     * 
     * @param string $recordVar
     * @param Omeka_Record_AbstractRecord $record
     * @param bool $setPreviousRecord
     */
    public function setCurrentRecord($recordVar, Omeka_Record_AbstractRecord $record, $setPreviousRecord = false)
    {
        $recordVar = $this->view->singularize($recordVar);
        if ($setPreviousRecord) {
            $previousRecordVar = "previous_$recordVar";
            $this->view->$previousRecordVar = $this->view->$recordVar;
        }
        $this->view->$recordVar = $record;
    }
}

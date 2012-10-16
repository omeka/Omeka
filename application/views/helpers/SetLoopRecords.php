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
class Omeka_View_Helper_SetLoopRecords extends Zend_View_Helper_Abstract
{
    /**
     * Set records to the view for iteration.
     * 
     * @param string $recordsVar
     * @param array $records
     */
    public function setLoopRecords($recordsVar, array $records)
    {
        $recordsVar = $this->view->pluralize($recordsVar);
        $this->view->$recordsVar = $records;
    }
}

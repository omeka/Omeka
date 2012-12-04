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
class Omeka_View_Helper_HasLoopRecords extends Zend_View_Helper_Abstract
{
    /**
     * Check if records have been set to the view for iteration.
     * 
     * Note that this method will return false if the records variable is set 
     * but is an empty array, unlike Omeka_View_Helper_GetLoopRecords::getLoopRecords(), 
     * which will return the empty array.
     * 
     * @param string $recordsVar
     * @return bool
     */
    public function hasLoopRecords($recordsVar)
    {
        $recordsVar = $this->view->pluralize($recordsVar);
        if (!$this->view->$recordsVar) {
            return false;
        }
        return true;
    }
}

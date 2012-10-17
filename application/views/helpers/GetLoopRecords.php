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
class Omeka_View_Helper_GetLoopRecords extends Zend_View_Helper_Abstract
{
    /**
     * Get records from the view for iteration.
     * 
     * @param string $recordsVar
     * @return array|null
     */
    public function getLoopRecords($recordsVar, $throwException = true)
    {
        $recordsVar = $this->view->pluralize($recordsVar);
        if (!$this->view->$recordsVar) {
            if ($throwException) {
                throw new Omeka_View_Exception(__("A loop %s variable has not been set to this view.", $recordsVar));
            }
            return false;
        }
        return $this->view->$recordsVar;
    }
}

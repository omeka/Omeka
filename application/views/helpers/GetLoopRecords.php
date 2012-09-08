<?php
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

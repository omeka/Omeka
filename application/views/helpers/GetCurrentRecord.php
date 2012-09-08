<?php
class Omeka_View_Helper_GetCurrentRecord extends Zend_View_Helper_Abstract
{
    /**
     * Get the current record from the view.
     * 
     * @throws Omeka_View_Exception
     * @param string $recordVar
     * @param bool $throwException
     * @return Omeka_Record_AbstractRecord|false
     */
    public function getCurrentRecord($recordVar, $throwException = true)
    {
        $recordVar = $this->view->singularize($recordVar);
        if (!$this->view->$recordVar) {
            if ($throwException) {
                throw new Omeka_View_Exception(__("A current %s has not been set to this view.", $recordVar));
            }
            return false;
        }
        return $this->view->$recordVar;
    }
}

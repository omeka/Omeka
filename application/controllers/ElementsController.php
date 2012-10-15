<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class ElementsController extends Omeka_Controller_AbstractActionController
{
    public function elementFormAction()
    {        
        $elementId = (int)$_POST['element_id'];
        $recordType = $_POST['record_type'];
        $recordId  = (int)$_POST['record_id'];
                         
        // Re-index the element form posts so that they are displayed in the correct order
        // when one is removed.
        $_POST['Elements'][$elementId] = array_merge($_POST['Elements'][$elementId]);

        $element = $this->_helper->db->getTable('Element')->find($elementId);
        $record = $this->_helper->db->getTable($recordType)->find($recordId);
        
        if (!$record) {
            $record = new $recordType;            
        }
        
        $this->view->assign(compact('element', 'record'));
    }
}
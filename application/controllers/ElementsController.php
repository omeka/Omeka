<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * All URLs for elements are routed through this controller.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
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
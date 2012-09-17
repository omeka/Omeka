<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ElementSetsController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ElementSet');
    }
    
    protected function _getDeleteConfirmMessage($record)
    {
        return __('This will delete the element set and all elements assigned to '
             . 'the element set. Items will lose all metadata that is specific '
             . 'to this element set.');
    }
    /**
     * Can't add element sets via the admin interface, so disable these
     * actions from being POST'ed to.
     * 
     * @return void
     */
    public function addAction()
    {
        throw new Omeka_Controller_Exception_403();
    }
    
    public function editAction()
    {
        $elementSet = $this->_helper->db->findById();
        
        // Do not allow editing of item type elements.
        if (ELEMENT_SET_ITEM_TYPE == $elementSet->name) {
            $this->_helper->redirector('index');
        }
        
        $db = $this->_helper->db;
        
        // Handle a submitted edit form.
        if ($elements = $this->getRequest()->getPost('elements')) {
            
            // Establish a valid element order.
            $order = array();
            foreach ($elements as $id => $element) {
                $order[$id] = (int) $element['order'];
            }
            asort($order); // sort preserving keys
            $i = 1;
            foreach ($order as $id => $orderNumber) {
                $elements[$id]['order'] = $i;
                $i++;
            }
            
            // Delete existing element order to prevent duplicate indices.
            $db->getDb()->update(
                $db->getDb()->Element, 
                array('order' => null), 
                array('element_set_id' => $this->getRequest()->getParam('id'))
            );
            
            // Update the elements.
            try {
                foreach ($elements as $id => $element) {
                    $elementRecord = $db->getTable('Element')->find($id);
                    $elementRecord->comment = trim($element['comment']);
                    $elementRecord->order = $element['order'];
                    $elementRecord->save();
                }
                $this->_helper->flashMessenger(__('The element set was successfully changed!'), 'success');
                $this->_helper->redirector('index');
            } catch (Omeka_Validator_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }
        
        $this->view->assign('elementSet', $elementSet);
    }
    
    protected function _redirectAfterEdit($record)
    {
        $this->_helper->redirector('index');
    }
}

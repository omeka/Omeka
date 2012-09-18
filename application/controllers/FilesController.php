<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * All URLs for files are routed through this controller.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class FilesController extends Omeka_Controller_AbstractActionController
{
    public $contexts = array(
        'show' => array('omeka-xml', 'omeka-json')
    );
    
    public function init()
    {
        $this->_helper->db->setDefaultModelName('File');
    }

    public function indexAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    
    public function browseAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    
    public function addAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    
    public function editAction()
    {
        $elementSets = $this->_getFileElementSets();
        $this->view->assign(compact('elementSets'));
        parent::editAction();
    }
    
    public function elementFormAction()
    {        
        $elementId = (int)$_POST['element_id'];
        $fileId  = (int)$_POST['file_id'];
                         
        // Re-index the element form posts so that they are displayed in the correct order
        // when one is removed.
        $_POST['Elements'][$elementId] = array_merge($_POST['Elements'][$elementId]);

        $element = $this->_helper->db->getTable('Element')->find($elementId);
                      
        try {
            $file = $this->_helper->db->findById($fileId);
        } catch (Omeka_Controller_Exception_404 $e) {
            $file = new File;
        }
        
        $this->view->assign(compact('element', 'file'));
    }
    
    protected function _getFileElementSets()
    {
        // Get element sets assigned to "All" and "File" record types.
         $elementSets = $this->_helper->db->getTable('ElementSet')->findByRecordType('File');

         // Remove legacy file element sets that will most likely be phased out 
         // in later versions.
         $legacyElementSetNames = array('Omeka Image File', 'Omeka Video File');
         foreach ($elementSets as $key => $elementSet) {
             if (in_array($elementSet->name, $legacyElementSetNames)) {
                 unset($elementSets[$key]);
             }
         }
         
         return $elementSets;
    }
    
    protected function _getDeleteConfirmMessage($record)
    {
        return __('This will delete the file and its associated metadata.');
    }
    
    protected function _redirectAfterDelete($record)
    {
        // Redirect back to the item show page for this file
        $this->_helper->flashMessenger('The file was successfully deleted.', 'success');
        $this->_helper->redirector('show', 'items', null, array('id'=>$record->item_id));
    }
}

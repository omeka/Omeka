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
class FilesController extends Omeka_Controller_Action
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
        // Get element sets assigned to "All" and "File" record types.
        $elementSets = $this->_helper->db->getTable('ElementSet')->findByRecordType('File');
        
        // Remove legacy file element sets that will most likely be phased out 
        // in later versions.
        foreach ($elementSets as $key => $elementSet) {
            if (in_array($elementSet->name, array('Omeka Image File', 'Omeka Video File'))) {
                unset($elementSets[$key]);
            }
        }
        
        $this->view->assign(compact('elementSets'));
        parent::editAction();
    }
}

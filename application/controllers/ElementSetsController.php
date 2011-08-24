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
class ElementSetsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'ElementSet';
    }
    
    protected function _getDeleteConfirmMessage($record)
    {
        return __('This will delete the element set and all elements assigned to '
             . 'the element set. Items will lose all metadata that is specific '
             . 'to this element set.');
    }
    /**
     * Can't add or edit element sets via the admin interface, so disable these
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
        throw new Omeka_Controller_Exception_403();
    }
}

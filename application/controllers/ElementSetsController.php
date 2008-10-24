<?php
require_once 'ElementSet.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ElementSetsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'ElementSet';
    }
    
    /**
     * Can't add or edit element sets via the admin interface, so disable these
     * actions from being POST'ed to.
     * 
     * @return void
     **/
    public function addAction()
    {
        throw new Omeka_Controller_Exception_403();
    }
    
    public function editAction()
    {
        throw new Omeka_Controller_Exception_403();
    }
}

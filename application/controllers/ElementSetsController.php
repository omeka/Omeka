<?php
require_once 'ElementSet.php';
/**
* 
*/
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
    {}
    
    public function editAction()
    {}
}

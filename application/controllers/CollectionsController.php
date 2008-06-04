<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Collection.php
 */ 
require_once 'Collection.php';

require_once 'Omeka/Controller/Action.php';

/**
 * CRUD controller for Collections
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class CollectionsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'Collection';
        
        $contextSwitch = $this->_helper->getHelper('ContextSwitch');
        $contextSwitch->addActionContext('remove-collector', 'json')
                      ->initContext();
    }
    
    /**
     * Ajax Action for removing collectors that are listed under collections.
     * 
     * This will either render a JSON from the Ajax request or, if Javascript
     * is not enabled, it will redirect to the 'show' page for the collection.
     * 
     * @return void
     **/
    public function removeCollectorAction()
    {
        $collection = $this->findById($this->_getParam('collection_id'));
        $collector  = $this->findById($this->_getParam('collector_id'), 'Entity');
                
        $this->view->result = $collection->removeCollector($collector);
                
        //If the request is not done through AJAX, redirect
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->redirect->goto('show', null, 
                                  null, array('id' => $collection->id));
        }
    }
}
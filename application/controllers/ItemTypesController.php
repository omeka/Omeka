<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Omeka_Controller_Action
 **/
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemTypesController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'ItemType';
    }
    
    /**
     * Ajax Action for adding metafields to the type form
     * 
     * @return void
     **/
    public function addMetafieldAction()
    {
        //If we're going to add a metafield that already exists, grab a current list        
       if ($this->_getParam('exists') == 'true') {           
           $metafields = $this->getTable('Metafield')->findAll();
           $this->view->assign(compact('metafields'));
           $this->render('existing-metafield');
       } else {
           $this->render('new-metafield');
       }
    }
    
    public function editAction()
    {
        $this->view->elements = get_db()->getTable('Element')->findBySql('es.name = ?', array('Item Type'));
        parent::editAction();
        
    }
}
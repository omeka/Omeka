<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * CRUD controller for Collections
 *
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2010
 **/
class CollectionsController extends Omeka_Controller_Action
{
    protected $_browseRecordsPerPage = 10;
        
    public function init()
    {
        $this->_modelClass = 'Collection';        
    }
    
    protected function _getAddSuccessMessage($record)
    {
        $collection = $record;
        return 'The collection "' . $collection->name . '" was successfully added!';        
    }
    
    protected function _getEditSuccessMessage($record)
    {
        $collection = $record;
        return 'The collection "' . $collection->name . '" was successfully changed!';        
    }
    
    protected function _getDeleteSuccessMessage($record)
    {
        $collection = $record;
        return 'The collection "' . $collection->name . '" was successfully deleted!';        
    }    
}
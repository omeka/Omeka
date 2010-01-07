<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Helpers extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $this->getBootstrap()->bootstrap('Db');
        $this->_initDbHelper();
    }
    
    private function _initDbHelper()
    {
        $dbHelper = new Omeka_Controller_Action_Helper_Db(
            $this->getBootstrap()->getResource('Db'));
        Zend_Controller_Action_HelperBroker::addHelper($dbHelper);
    }
}

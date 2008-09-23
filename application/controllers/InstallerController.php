<?php
/**
 * 
 * @param string
 * @return void
 **/
 
/**
 * @todo The entire install process should eventually be routed through this
 * controller. For the moment, it's enough that this correctly redirects to the
 * install script directory.
 *
 *
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class InstallerController extends Zend_Controller_Action
{
    /**
     * @todo This should encapsulate the entire install script.
     **/
    public function indexAction()
    {
        
    }
    
    /**
     * Notify the user that Omeka must be installed.
     * 
     **/
    public function notifyAction()
    {

    }
    
    /**
     * All actions besides the ones hard coded into this controller should
     *  redirect back to 'index'.
     * 
     **/
    public function __call($m, $a)
    {
        $this->_forward('index');
    }
}

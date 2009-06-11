<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

abstract class Omeka_Core_Resource_ResourceAbstract extends Zend_Application_Resource_ResourceAbstract
{
    public function setCore(Omeka_Core $core)
    {
        $this->_core = $core;
    }
    
    public function getBootstrap()
    {
        return ($bootstrap = parent::getBootstrap()) ? $bootstrap : $this->_core;
    }
}

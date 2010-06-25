<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Load the default configuration file for Omeka.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Core_Resource_Config extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {        
        return new Zend_Config_Ini(CONFIG_DIR . DIRECTORY_SEPARATOR . 'config.ini', 'site');     
    }
}

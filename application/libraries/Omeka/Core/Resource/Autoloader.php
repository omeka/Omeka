<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * An application resource for class autoloaders.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Core_Resource_Autoloader extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        // TODO: [2.0] Add a namespace for models (Omeka_File or Omeka_Model_File?)
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => APP_DIR,
            'namespace'     => 'Omeka',
            'resourceTypes' => array(
                'form' => array(
                    'path'      => 'forms/',
                    'namespace' => 'Form',
                )
            )        
        ));
    }
}

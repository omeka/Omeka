<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An application resource for class autoloaders.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Autoloader extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Register autoloaders. 
     *
     * Set up autoloading of the following class types from the following
     * directories:
     * - {@link Omeka_Form}: forms/
     */
    public function init()
    {
        new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APP_DIR, 
            'namespace' => 'Omeka', 
            'resourceTypes' => array(
                'form' => array(
                    'path' => 'forms/', 
                    'namespace' => 'Form', 
                ),
                'view_helper' => array(
                    'path' => 'views/helpers', 
                    'namespace' => 'View_Helper', 
                ), 
                'action_helper' => array(
                    'path' => 'controllers/helpers', 
                    'namespace' => 'Controller_Action_Helper', 
                ), 
            )
        ));
    }
}

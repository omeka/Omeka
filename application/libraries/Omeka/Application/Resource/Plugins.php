<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Fire the 'initialize' hook for all installed plugins.
 * 
 * Note that this hook fires before the front controller has been initialized or
 * dispatched.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Plugins extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Pluginbroker');
        $broker = $bootstrap->getResource('Pluginbroker');
        // Fire all the 'initialize' hooks for the plugins
        $broker->callHook('initialize');
    }
}

<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Fire the 'initialize' hook for all installed plugins.  Note that
 * this hook fires before the front controller has been initialized or
 * dispatched.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Plugins extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Pluginbroker');
        $broker = $bootstrap->getResource('Pluginbroker');
        // Fire all the 'initialize' hooks for the plugins
        $broker->initialize();
    }
}

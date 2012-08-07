<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Core resource for configuring caches for use by other components.
 *
 * @package Omeka
 */
class Omeka_Application_Resource_Cachemanager extends Zend_Application_Resource_Cachemanager {
    
    /**
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');

        $cacheConfig = $config->cache;
        if ($cacheConfig) {
            $this->setOptions($cacheConfig->toArray());
        }

        return parent::init();
    }
}

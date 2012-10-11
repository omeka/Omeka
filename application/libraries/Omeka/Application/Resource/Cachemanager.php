<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Core resource for configuring caches for use by other components.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Cachemanager extends Zend_Application_Resource_Cachemanager
{
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

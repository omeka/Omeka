<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Set up the plugin hooks for the Main Navigation
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Mainnavigation extends Zend_Application_Resource_ResourceAbstract
{    
    public function init()
    {        
        // We need the front controller to be set up if we're initializing the
        // Theme component.
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Pluginbroker');
        $broker = $bootstrap->getResource('Pluginbroker');
        
        // Add hooks for plugin activation, deactivation, installation, and uninstallation
        $broker->addInternalHook('activate', array($this, 'updateMainNavigation'));
        $broker->addInternalHook('deactivate', array($this, 'updateMainNavigation'));
        $broker->addInternalHook('install', array($this, 'updateMainNavigation'));
        $broker->addInternalHook('uninstall', array($this, 'updateMainNavigation'));
    }
    
    public function updateMainNavigation()
    {
        set_theme_base_url('public');    
        $nav = new Omeka_Navigation();
        $nav->loadAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);        
        $nav->addPagesFromFilter(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_FILTER_NAME);
        $nav->saveAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);
        // Reset to "current" base uri. "revert" won't work here because
        // something may have used public_uri or admin_uri in between.
        set_theme_base_url();
    }
}
<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Core resource for configuring and loading the translation and locale 
 * components.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Locale extends Zend_Application_Resource_Locale {
    
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');
        
        $locale = $config->locale;
        if ($locale instanceof Zend_Config) {
            $localeName = $locale->name;
            $cache = $locale->cache;
        } else {
            $localeName = $locale;
            $cache = null;
        }
        
        if ($this->getBootstrap()->hasResource('Pluginbroker')) {
            $broker = $this->getBootstrap()->getResource('Pluginbroker');
            $localeName = $broker->applyFilters('locale', $localeName);
        }
        
        if ($localeName) {
            if ($cache === null) {
                $cache = 'locale';
            }
            
            $this->setOptions(array(
                'default' => $localeName,
                'cache' => $cache
            ));
            $this->_setTranslate($localeName, $cache);
        }
        
        return parent::init();
    }
    
    /**
     * Retrieve translation configuration options.
     * 
     * @return string
     */
    private function _setTranslate($locale, $cache)
    {
        $options = array(
            'bootstrap' => $this->getBootstrap(),
            'locale' => $locale,
            'adapter' => 'gettext',
            'disableNotices' => true,
            'cache' => $cache
        );
        
        $translatePath = LANGUAGES_DIR . "/$locale.mo";
        if (is_readable($translatePath)) {
            $options['content'] = $translatePath;
        } else {
            $options['content'] = '';
        }
        
        $translateResource = new Zend_Application_Resource_Translate($options);
        $translateResource->getTranslate();
    }
}

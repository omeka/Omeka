<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Center for History and New Media, 2009-2011
 */
 
class Omeka_Core_Resource_Translate extends Zend_Application_Resource_Translate {
    
    /**
     * @return void
     */
    public function init()
    {
        $config = $this->_getTranslateConfig();

        // If no language is selected, don't load the Translate component.
        if (isset($config['locale'])) {
            $config['content'] = LANGUAGES_DIR . "/{$config['locale']}.mo";
            $this->setOptions($config);
            return parent::init();
        } else {
            return null;
        }
    }

    /**
     * Retrieve translation configuration options.
     * 
     * @return string
     */
    private function _getTranslateConfig()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');
        $translateConfig = isset($config->translate) 
                       ? $config->translate->toArray()
                       : array();
        
        $translateConfig['disableNotices'] = true;

        return $translateConfig;
    }
}

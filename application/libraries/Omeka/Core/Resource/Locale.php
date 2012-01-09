<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Core resource for configuring and loading the translation and locale
 * components.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 */
class Omeka_Core_Resource_Locale extends Zend_Application_Resource_Locale {
    
    /**
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');
        if (($locale = $config->locale)) {
            $this->setOptions(array('default' => $locale));
            $this->_setTranslate($locale);
        }

        return parent::init();
    }

    /**
     * Retrieve translation configuration options.
     * 
     * @return string
     */
    private function _setTranslate($locale)
    {
        $translate = new Zend_Translate(array(
            'adapter' => 'gettext',
            'disableNotices' => true,
        ));

        $translationSources = array(LANGUAGES_DIR);
        if ($pluginBroker = $this->getBootstrap()->getResource('PluginBroker')) {
            $translationSources = $pluginBroker->applyFilters('translation_sources', $translationSources);
        }

        foreach ($translationSources as $dir) {
            try {
                $translate->addTranslation(array(
                    'content' => $dir . "/$locale.mo",
                    'locale' => $locale
                ));
            } catch (Zend_Translate_Exception $e) {
                // Do nothing, allow the user to set a locale without a
                // translation.
            }
        }

        Zend_Registry::set(
            Zend_Application_Resource_Translate::DEFAULT_REGISTRY_KEY,
            $translate);
    }
}

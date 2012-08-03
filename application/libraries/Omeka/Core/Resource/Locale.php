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
            'content' => LANGUAGES_DIR . "/$locale.mo",
            'cache' => $cache
        );

        $translateResource = new Zend_Application_Resource_Translate($options);

        try {
            $translateResource->getTranslate();
        } catch (Zend_Translate_Exception $e) {
            // Do nothing, allow the user to set a locale without a
            // translation.
        }
    }
}

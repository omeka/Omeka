<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 **/
class Omeka_Core_Resource_Htmlpurifier extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {           
        // Get the bootstrap
        $bootstrap = $this->getBootstrap();
        
        // Get the config resource
        $bootstrap->bootstrap('Config');
        $config = $bootstrap->getResource('Config');
                
        // Get the HtmlPurifier controller plugin        
        $htmlPurifier = $this->_getHtmlPurifier($config);
                        
        return $htmlPurifier;
    }
    
    protected function _getHtmlPurifier($config)
    {   
        // Do not purify if the POST is empty.
        if (empty($_POST) || !$config->htmlpurifier->enabled) {
            return;
        }

        // Get the allowed HTML tags from the configuration file
        $allowedHtmlTags = $config->htmlpurifier->allowedhtmltags;
        if ($allowedHtmlTags === null) {
            return;
        }

        // Require the HTML Purfier autoloader.
        require_once 'htmlpurifier-3.1.1-lite/library/HTMLPurifier.auto.php';        
        $htmlPurifierConfig = HTMLPurifier_Config::createDefault();

        // Allow HTML tags. Setting this as NULL allows a subest of TinyMCE's 
        // valid_elements whitelist. Setting this as an empty string disallows 
        // all HTML elements.
        $htmlPurifierConfig->set('HTML', 'Allowed', $allowedHtmlTags);

        // Disable caching.
        $htmlPurifierConfig->set('Cache', 'DefinitionImpl', null);

        // Get the purifier as a singleton.
        $htmlPurifier = HTMLPurifier::instance($htmlPurifierConfig);

        // Set this in the registry so that other plugins can get to it.
        Zend_Registry::set('html_purifier', $htmlPurifier);
                
        return $htmlPurifier;
    }      
}

<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * A Zend_Filter implementation that uses HtmlPurifier to purify a string
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Filter_HtmlPurifier implements Zend_Filter_Interface
{
    protected static $_purifier = null;
    
    /**
     * Filter the value
     * 
     * @param string
     * @return string An html purified string
     **/
    public function filter($value)
    {
        $purifier = self::getHtmlPurifier();
        return $purifier->purify($value);
    }
    
    public static function getDefaultAllowedHtmlElements()
    {
         $defaultAllowedHtmlElements = array('p',
                                            'br',
                                            'strong',
                                            'em',
                                            'span',
                                            'div',
                                            'ul',
                                            'ol',
                                            'li',
                                            'a',
                                            'h1',
                                            'h2',
                                            'h3',
                                            'h4',
                                            'h5',
                                            'h6',
                                            'address',
                                            'pre',
                                            'table',
                                            'tr',
                                            'td',
                                            'blockquote',
                                            'thead',
                                            'tfoot',
                                            'tbody',
                                            'th',
                                            'dl',
                                            'dt',
                                            'dd',
                                            'q',
                                           'small',
                                           'strike',
                                           'sup',
                                           'sub',
                                           'b',
                                           'i',
                                           'big',
                                           'small',
                                           'tt');
       return $defaultAllowedHtmlElements; 
    }
    
    public static function getDefaultAllowedHtmlAttributes()
    {
        $defaultAllowedHtmlAttributes = array('*.style',
                                              '*.class',
                                              'a.href',
                                              'a.title',
                                              'a.target');
        return $defaultAllowedHtmlAttributes;
    }
             
    public static function getHtmlPurifier()
    {
        if (!self::$_purifier) {
            self::$_purifier = self::createHtmlPurifier();
        }
        return self::$_purifier;
    }
    
    /**
     * @param HTMLPurifier $purifier
     **/
    public static function setHtmlPurifier($purifier)
    {
        self::$_purifier = $purifier;
        
        // Set this in the registry so that other plugins can get to it.
        Zend_Registry::set('html_purifier', $htmlPurifier);
    }
    
    public static function createHtmlPurifier($allowedHtmlElements=null, $allowedHtmlAttributes=null)
    {
        if ($allowedHtmlElements === null || $allowedHtmlAttributes === null) {

            // Get the allowed HTML elements from the configuration file
            if ($allowedHtmlElements === null) {
                $allowedHtmlElements = get_option('html_purifier_allowed_html_elements');
            }

            // Get the allowed HTML attributes from the configuration file
            if ($allowedHtmlAttributes === null) {
                $allowedHtmlAttributes = get_option('html_purifier_allowed_html_attributes');
            }
        }

        // Require the HTML Purfier autoloader.
        require_once 'htmlpurifier-3.1.1-lite/library/HTMLPurifier.auto.php';        
        $purifierConfig = HTMLPurifier_Config::createDefault();
        
        // Set the encoding to UTF-8
        $purifierConfig->set('Core', 'Encoding', 'UTF-8');

        // Allow HTML tags. Setting this as NULL allows a subest of TinyMCE's 
        // valid_elements whitelist. Setting this as an empty string disallows 
        // all HTML elements.
        $purifierConfig->set('HTML', 'AllowedElements', $allowedHtmlElements);
        $purifierConfig->set('HTML', 'AllowedAttributes', $allowedHtmlAttributes);

        // Disable caching.
        $purifierConfig->set('Cache', 'DefinitionImpl', null);

        // Get the purifier as a singleton.
        $purifier = HTMLPurifier::instance($purifierConfig);
                
        return $purifier;
    }
}
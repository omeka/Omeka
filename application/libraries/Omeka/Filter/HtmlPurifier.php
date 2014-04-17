<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A Zend_Filter implementation that uses HtmlPurifier to purify a string.
 * 
 * @package Omeka\Filter
 */
class Omeka_Filter_HtmlPurifier implements Zend_Filter_Interface
{
    private static $_purifier = null;
    
    private static $_purifierConfig = array(
        'Core.Encoding' => 'UTF-8',
        'Cache.DefinitionImpl' => null, // Caching disabled
        'Attr.AllowedFrameTargets' => array('_blank'),
        'HTML.TidyLevel' => 'none',
        'HTML.AllowedElements' => array(
            'p', 'br', 'strong', 'em', 'span', 'div', 'ul', 'ol', 'li', 'a', 
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'address', 'pre', 'table',
            'tr', 'td', 'blockquote', 'thead', 'tfoot', 'tbody', 'th', 'dl', 
            'dt', 'dd', 'q', 'small', 'strike', 'sup', 'sub', 'b', 'i', 'big', 
            'small', 'tt'
        ),
        'HTML.AllowedAttributes' => array(
            '*.style', '*.class', 'a.href', 'a.title', 'a.target'
        ),
        // Note: this allows "unsafe" elements/attributes, but only when they
        // are also in the Allowed lists configured by the user.
        'HTML.Trusted' => true
    );

    /**
     * Filter the value
     * 
     * @param string
     * @return string An html purified string
     */
    public function filter($value)
    {
        $purifier = self::getHtmlPurifier();
        $cleanValue = $purifier->purify($value);
        return $cleanValue;
    }
        
    /**
     * Get the default allowed html elements.
     *
     * @return array An array of strings corresponding to the allowed html elements
     */
    public static function getDefaultAllowedHtmlElements()
    {
       return self::$_purifierConfig['HTML.AllowedElements'];
    }
    
    /**
     * Get the default allowed html attributes.
     *
     * @return array An array of strings corresponding to the allowed html 
     * attributes
     */
    public static function getDefaultAllowedHtmlAttributes()
    {
        return self::$_purifierConfig['HTML.AllowedAttributes'];
    }
    
    /**
     * Gets the html purifier singleton
     *
     * @return HTMLPurifier $purifier
     */         
    public static function getHtmlPurifier()
    {
        if (!self::$_purifier) {
            self::$_purifier = self::createHtmlPurifier();
        }
        return self::$_purifier;
    }
    
    /**
     * Sets the html purifier singleton
     *
     * @param HTMLPurifier $purifier
     * @return void
     */
    public static function setHtmlPurifier($purifier)
    {
        self::$_purifier = $purifier;
        
        // Set this in the registry so that other plugins can get to it.
        Zend_Registry::set('html_purifier', $purifier);
    }
    
    /**
     * @param array $allowedHtmlElements An array of strings representing 
     * allowed HTML elements
     * @param array $allowedHtmlAttributes An array of strings representing 
     * allowed HTML attributes
     * @return HTMLPurifier 
     **/
    public static function createHtmlPurifier(
        $allowedHtmlElements = null, 
        $allowedHtmlAttributes = null
    ) {
        // Require the HTML Purfier autoloader.
        require_once 'htmlpurifier/HTMLPurifier.auto.php';        

        // Get the allowed HTML elements from the configuration file
        // Setting this as NULL allows a subest of TinyMCE's 
        // valid_elements whitelist. Setting this as an empty string disallows 
        // all HTML elements.
        if ($allowedHtmlElements === null) {
            $allowedHtmlElements = explode(',', 
                get_option('html_purifier_allowed_html_elements'));
        }

        // Get the allowed HTML attributes from the configuration file
        if ($allowedHtmlAttributes === null) {
            $allowedHtmlAttributes = explode(',', 
                get_option('html_purifier_allowed_html_attributes'));
        }


        // Filter the allowed html attributes of any attributes that are 
        // missing elements.
        // For example, if there is no 'a' element then filter out the 
        // attribute 'a.href' and any other attribute associated with the 'a' 
        // element
        $allowedHtmlAttributes = 
            self::filterAttributesWithMissingElements(
                $allowedHtmlAttributes, $allowedHtmlElements);

        $purifierConfig = HTMLPurifier_Config::createDefault();
        foreach (self::$_purifierConfig as $key => $value) {
            $purifierConfig->set($key, $value);
        }
        $purifierConfig->set('HTML.AllowedElements', $allowedHtmlElements);
        $purifierConfig->set('HTML.AllowedAttributes', $allowedHtmlAttributes);

        $purifier = HTMLPurifier::instance($purifierConfig);
        return $purifier;
    }
    
    public static function filterAttributesWithMissingElements(
        $htmlAttributes = array(), 
        $htmlElements = array()
    ) {
        if (!count($htmlElements)) {
            return array();
        }
        $cleanHtmlAttributes = array();
        foreach($htmlAttributes as $attr) {
            $attr = trim($attr);
            $attrParts = explode('.', $attr);
            if (count($attrParts) == 2 
                && ($attrParts[0] == '*' 
                    || in_array($attrParts[0], $htmlElements))
            ) {
                $cleanHtmlAttributes[] = $attr; 
            }
        }
        return $cleanHtmlAttributes;
    }
}

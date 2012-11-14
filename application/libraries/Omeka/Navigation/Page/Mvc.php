<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Customized subclass of Zend Framework's Zend_Navigation_Page_Mvc class.
 * 
 * @package Omeka\Navigation
 */
class Omeka_Navigation_Page_Mvc extends Zend_Navigation_Page_Mvc
{
    /**
     * Theme option to use when assembling URL
     *
     * @var string
     */
    protected $_theme;
        
    /**
     * Returns href for this page
     *
     * This method uses {@link Zend_Controller_Action_Helper_Url} to assemble
     * the href based on the page's properties.
     *
     * @return string  page href
     */
    public function getHref()
    {
        $themeOption = $this->_theme;
        if ($themeOption === null) {
            return parent::getHref();
        }
        
        // clear cache if themeOption is the empty string,
        // which corresponds to current base url
        if ($themeOption == '') {
            $this->_hrefCache = null;
        }
        
        // create an href for the correct theme
        set_theme_base_url($themeOption);
        $href = parent::getHref();
        revert_theme_base_url();
        return $href;
    }
    
    /**
     * Returns theme option to use when assembling URL
     *
     * @see getHref()
     *
     * @return string|null  theme option
     */
    public function getTheme()
    {
        return $this->_theme;
    }
    
    /**
     * Sets theme option to use when assembling URL
     *
     * @see getHref()
     *
     * @param  string $theme             theme option 'admin' or 'public'
     * @return Omeka_Navigation_Page_Mvc   fluent interface, returns self
     * @throws Zend_Navigation_Exception  if invalid $theme is given
     */
    public function setTheme($theme)
    {
        if (null !== $theme && !is_string($theme)) {
            require_once 'Zend/Navigation/Exception.php';
            throw new Zend_Navigation_Exception(
                    'Invalid argument: $theme must be a string or null');
        }

        $this->_theme = $theme;
        $this->_hrefCache = null;
        return $this;
    }
}

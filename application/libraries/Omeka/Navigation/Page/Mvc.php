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
     * Returns href for this page
     *
     * This method uses {@link Zend_Controller_Action_Helper_Url} to assemble
     * the href based on the page's properties.
     *
     * @return string  page href
     */
    public function getHref()
    {
        $themeOption = $this->theme;
        if (!$themeOption) {
            $themeOption = 'public';
        }
        
        // create an href for the correct theme
        set_theme_base_url($themeOption);
        $href = parent::getHref();
        set_theme_base_url();
        return $href;
    }
}
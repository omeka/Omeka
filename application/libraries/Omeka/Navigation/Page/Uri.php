<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Customized subclass of Zend Framework's Zend_Navigation_Page_Uri class.
 *
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Navigation_Page_Uri extends Zend_Navigation_Page_Uri
{
    /**
     * Returns whether page should be considered active or not
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          active if any child pages are active. Default is
     *                          false.
     * @return bool             whether page should be considered active
     */
    public function isActive($recursive = false)
    {
        $this->_active = is_current_uri($this->getUri());
        return parent::isActive($recursive);
    }
    
    /**
     * Sets page URI
     *
     * @param  string $uri                page URI, must a string or null
     * @return Omeka_Navigation_Page_Uri   fluent interface, returns self
     * @throws Zend_Navigation_Exception  if $uri is invalid
     */
    public function setHref($href)
    {
        if ($hrefData = $this->_normalizeHref($href)) {
            $this->setUri($hrefData['uri']);
            if ($hrefData['fragment'] !== false) {
                $this->setFragment($hrefData['fragment']);
            }                    
        }
    }
    
    /**
     * Normalizes a string href for a navigation page and returns an array with the following keys:
     * 'uri' => the uri of the href. 
     * 'fragment' => the fragment of the href 
     * If the $href is a relative path, then it must be a root path.
     * If $href is an invalid uri, then return null.  
     *
     * @param String $href
     * @return array|null
     */
    private function _normalizeHref($href) 
    {                
        if ($href !== null) {       
            if (strlen($href) && $href[0] == '/') {
                $href = substr(WEB_ROOT, 0, strrpos(WEB_ROOT, PUBLIC_BASE_URL)) . $href;
            }
            try {
                $uri = Zend_Uri::factory($href);
                if ($uri->valid()) {
                    $fragment = $uri->getFragment();
                    $uri->setFragment('');
                    return array(
                        'uri' => $uri->getUri(),
                        'fragment' => $fragment, 
                    );
                }
            } catch (Zend_Uri_Exception $e) {
            }
        }
        return null;
    }
}
<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Customized subclass of Zend Framework's Zend_Navigation_Page_Uri class.
 * 
 * @package Omeka\Navigation
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
        $this->_active = is_current_url($this->getUri());
        return parent::isActive($recursive);
    }
    
    /**
     * Sets page href.  It will parse the href and update the uri and fragment properties.
     *
     * @param  string|null $href            page href, must a string or null
     * @return Omeka_Navigation_Page_Uri   fluent interface, returns self
     * @throws Omeka_Navigation_Page_Uri_Exception  if $uri is invalid
     */
    public function setHref($href)
    {
        $hrefData = $this->_normalizeHref($href);
        $this->setUri($hrefData['uri']);
        $this->setFragment($hrefData['fragment']);
    }
    
    /**
     * Normalizes a string href for a navigation page and returns an array with the following keys:
     * 'uri' => the uri of the href. 
     * 'fragment' => the fragment of the href 
     * If the $href is a relative path, then it must be a root path.
     * If the $href is a relative path then the value for the 'uri' key will be a relative path.
     * If $href is an invalid uri, then return null.  
     *
     * @param String $href
     * @return array
     * @throws Omeka_Navigation_Page_Uri_Exception  if $uri is invalid
     */
    protected function _normalizeHref($href) 
    {   
        if ($href === null || trim($href) == '') {
            return array(
                'uri' => '',
                'fragment' => null
            );
        }
        $href = trim($href);            
        $isPath = false;
        if (strlen($href) && $href[0] == '/') {
            // attempt to convert root path into a full path, 
            // so that we can later extract the fragment using Zend_Uri_Http
            $webRoot = trim(WEB_ROOT);
            $webPath = trim(PUBLIC_BASE_URL);
            if ($webPath == '') {
                $href = $webRoot . $href;
            } else {
                $index = strrpos($webRoot, $webPath);
                if ($index !== false) {
                    $href = substr($webRoot, 0, $index) . $href;
                } else {
                    $href = $webRoot . $href;
                }
            }
            $isPath = true;
        }
        try {
            $uri = Zend_Uri::factory($href);
            if ($uri->valid()) {
                $fragment = $uri->getFragment();
                if (!$fragment) {
                    $fragment = null;
                }
                $uri->setFragment('');
                if ($isPath) {
                    $uriString = $uri->getPath();
                    if ($query = $uri->getQuery()) {
                        $uriString .= '?' . $query; 
                    }
                    $uri = $uriString;
                } else {
                    $uri = $uri->getUri();
                }
                if (strpos($href, '#')) {
                    $uri .= '#'; // add the hash to the uri
                }
                return array(
                    'uri' => $uri,
                    'fragment' => $fragment, 
                );
            }
        } catch (Zend_Uri_Exception $e) {
            if (filter_var($href, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
                $fragmentPos = strrpos($href, '#');
                if ($fragmentPos !== FALSE) {
                    if ($fragmentPos < strlen($href) - 1) {
                        $fragment = substr($href, $fragmentPos + 1); // exclude the hash from the fragment
                    } else {
                        $fragment = null;
                    }
                    $uri = substr($href, 0, $fragmentPos + 1); // include the hash in the uri
                } else {
                    $uri = $href;
                    $fragment = null;
                }
                return array(
                    'uri' => $uri,
                    'fragment' => $fragment
                );
            }
        }
        throw new Omeka_Navigation_Page_Uri_Exception(__('Invalid URI for Omeka_Navigation_Page_Uri object: %s', $href));
    }
}
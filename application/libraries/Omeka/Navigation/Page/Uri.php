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
}
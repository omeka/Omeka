<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * A Zend_Filter implementation that converts any boolean value passed to 
 * it to an integer: 1 or 0.
 * 
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Filter_Boolean implements Zend_Filter_Interface
{
    /**
     * Filter the value
     * 
     * @param mixed
     * @return string "1" or "0"
     */
    public function filter($value)
    {
        return in_array($value, array('true', 'On', 'on', 1, "1", true), true)
            ? 1
            : 0;
    }
}

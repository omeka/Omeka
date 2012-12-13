<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A Zend_Filter implementation that converts any boolean value passed to it to 
 * an integer: 1 or 0.
 * 
 * @package Omeka\Filter
 */
class Omeka_Filter_Boolean implements Zend_Filter_Interface
{
    /**
     * Filter the value
     * 
     * @param mixed
     * @return int 1 or 0
     */
    public function filter($value)
    {
        return in_array($value, array('true', 'On', 'on', 1, "1", true), true)
            ? 1
            : 0;
    }
}

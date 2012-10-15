<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Converts input into values suitable for use as Omeka 'id' key values.
 * 
 * @package Omeka\Filter
 */
class Omeka_Filter_ForeignKey implements Zend_Filter_Interface
{
    /**
     * Convert any value into an unsigned integer that would be valid
     * if stored as a foreign key in a database table.
     *
     * This will return null for any value that falls outside the range
     * of an unsigned integer (string, negative numbers, etc.)
     * 
     * @param mixed $value Input value.
     * @return integer
     */
    public function filter($value)
    {
        if (empty($value) || ((int) $value <= 0)) {
            return null;
        }
        
        return (int) $value;
    }
}

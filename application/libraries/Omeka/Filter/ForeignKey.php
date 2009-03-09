<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/** 
 * @see Zend_Filter_Input
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Filter_ForeignKey implements Zend_Filter_Interface
{
    /**
     * Convert any value into an unsigned integer that would be valid
     * if stored as a foreign key in a database table.
     *
     * This will return null for any value that falls outside the range
     * of an unsigned integer (string, negative numbers, etc.)
     * 
     * @param mixed
     * @return integer
     **/
    public function filter($value)
    {
        if (empty($value) || ((int) $value <= 0)) {
            return null;
        }
        
        return (int) $value;
    }
}
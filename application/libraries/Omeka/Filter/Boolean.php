<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * A Zend_Filter implementation that converts any boolean form value passed to 
 * it to a string "1" or "0". 
 *
 * @todo This deserves some testing to see whether or not it should return 
 * a true boolean or a string representation (not sure how the database 
 * inserts booleans) and whether or not it can be tricked into returning the
 * wrong value under certain circumstances.
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
        return in_array($value, 
                        array('true', 'On', 'on', 1, "1", true), 
                        true) ? "1" : "0";
    }
}

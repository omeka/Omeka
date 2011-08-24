<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Validate a range of partial date values.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Validate_PartialDateRange extends Omeka_Validate_PartialDate
{
    /**
     * Delimeter between dates in a range.
     *
     * @var string
     */
    protected $_delimiter = ' ';
    
    /**
     * Validate, assuming a date range.
     * 
     * Start and end dates are separated by a single whitespace.
     * One or both of these dates can be empty, but if they aren't 
     * empty then they have to validate as dates.
     *
     * @param string $value]
     * @return boolean
     */ 
    public function isValid($value)
    {
        list($startDate, $endDate) = explode($this->_delimiter, $value);
        return (empty($startDate) or parent::isValid($startDate)) and (empty($endDate) or parent::isValid($endDate));
    }
}

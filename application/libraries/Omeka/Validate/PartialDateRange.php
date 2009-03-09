<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Validate_PartialDateRange extends Omeka_Validate_PartialDate
{
    protected $_delimiter = ' ';
    
    // Start and end dates are separated by a single whitespace.
    // One or both of these dates can be empty, but if they aren't 
    // empty then they have to validate as dates.
    public function isValid($value)
    {
        list($startDate, $endDate) = explode($this->_delimiter, $value);
        return (empty($startDate) or parent::isValid($startDate)) and (empty($endDate) or parent::isValid($endDate));
    }
}

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
class Omeka_Validate_PartialDate extends Zend_Validate_Regex
{    
    public function getDateRegex()
    {
        $year = '\b(?:\-?[0-9]{1,9})\b';
        $month = '\b(?:0[1-9]|1[0-2])\b';
        $day = '\b(?:0[1-9]|[1-2][0-9]|3[0-1])\b';

        return "\b(?:$year(?:\-$month(?:\-$day)?)?)\b";        
    }
    
    public function __construct()
    {
        $pattern = "/^" . $this->getDateRegex() . "$/u";
        parent::__construct($pattern);
    }
}

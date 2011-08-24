<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Converts to and from MySQL date representations.
 *
 * @todo This should be removed and replaced with a better date processing
 * algorithm, especially since we no longer rely on MySQL date fields for
 * storing dates in the Omeka database.
 * @deprecated
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Filter_Date
{
    /**
     * Convert a year, month and day into a valid MySQL date.
     *
     * If the resulting date would be 0000-00-00, return null instead.
     *  
     * @param string $year Numeric year value.
     * @param string $month Numeric month value.
     * @param string $day Numeric day value.
     * @return string|null
     */
    public function filter($year, $month, $day)
    {
        $date = array();
 
        if (!empty($year)) {
            $date[0] = str_pad($year, 4, '0', STR_PAD_LEFT);
        }

        if (!empty($month)) {
            $date[1] = str_pad($month, 2, '0', STR_PAD_LEFT);
        } else if (empty($month) and !empty($day)) {
            // If the month is empty but the day is not, it should put an empty
            // string in there as a place-holder.
            $date[1] = '';
        }        
        
        if (!empty($day)) {
            $date[2] = str_pad($day, 2, '0', STR_PAD_LEFT);
        }         
         
        $date = implode('-', $date);

        if (empty($date)) {
             return null;
        } else {
            return $date;
        }
    }
    
    /**
     * Split a valid MySQL date 
     * 
     * @param string $date
     * @return array Array of year, month and day values.
     */
    public function split($date)
    {
        $date_array = explode('-', $date);
        
        //Year, month, day
        return array('year'=>$date[0], 'month'=>$date[1], 'day'=>$date[2]);
    }
}

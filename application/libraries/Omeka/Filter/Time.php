<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Converts to and from MySQL time representations.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Filter_Time
{
    /**
     * Convert a hour, minute and second into a valid MySQL time.
     * If the resulting time would be 00:00:00, return null instead.
     * 
     * @param string $hour
     * @param string $minute
     * @param string $second
     * @return string|null
     */
    public function filter($hour, $minute, $second)
    {
        $time = array();
        $time[0] = !empty($hour)   ? str_pad($hour,   2, '0', STR_PAD_LEFT) : '00';
        $time[1] = !empty($minute) ? str_pad($minute, 2, '0', STR_PAD_LEFT) : '00';
        $time[2] = !empty($second) ? str_pad($second, 2, '0', STR_PAD_LEFT) : '00';        
        
        $time = implode(':', $time);
        
        if ($time == '00:00:00') {
            return null;
        }
        
        return $time;
    }
    
    /**
     * Split a valid MySQL time.
     * 
     * @param string $time
     * @return array Contains the following keys: 'hour', 'minute', 'second'.
     */
    public function split($time)
    {
        $time_array = explode(':', $time);
        
        // hour, minute, second
        return array('hour'   => $time[0], 
                     'minute' => $date[1], 
                     'second' => $date[2]);
    }
}

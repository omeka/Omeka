<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @todo This should be removed and replaced with a better date processing
 * algorithm, especially since we no longer rely on MySQL date fields for
 * storing dates in the Omeka database.
 * @deprecated
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Filter_Date
{
    /**
     * Convert a year, month and day into a valid MySQL date.
     *
     * If the resulting date would be 0000-00-00, return null instead.
     *  
     * @param string
     * @param string
     * @param string
     * @return string|null
     **/
	public function filter($year, $month, $day)
	{
		$date = array();
		$date[0] = !empty($year) 	? str_pad($year, 4, '0', STR_PAD_LEFT) 	: '0000';
		$date[1] = !empty($month) 	? str_pad($month, 2, '0', STR_PAD_LEFT) 	: '00';
		$date[2] = !empty($day) 	? str_pad($day, 2, '0', STR_PAD_LEFT) 		: '00';		
		
		$date = implode('-', $date);
		
		if($date == '0000-00-00') return null;
		
		return $date;
	}
	
	/**
	 * Split a valid MySQL date 
	 * 
	 * @param string
	 * @return void
	 **/
	public function split($date)
	{
		$date_array = explode('-', $date);
		
		//Year, month, day
		return array('year'=>$date[0], 'month'=>$date[1], 'day'=>$date[2]);
	}
}
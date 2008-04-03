<?php 
class Omeka_Filter_Date
{
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
	
	public function split($date)
	{
		$date_array = explode('-', $date);
		
		//Year, month, day
		return array('year'=>$date[0], 'month'=>$date[1], 'day'=>$date[2]);
	}
}	 
?>

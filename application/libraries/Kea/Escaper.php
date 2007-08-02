<?php 
/**
* Kea_Escaper
*
* Use this to help escape output for the application
*/
class Kea_Escaper
{
	/**
	 * @example 
	 *
	 * @return void
	 **/
	protected $avoid;
	
	public function __construct($avoidTags = false)
	{
		$this->avoid = $avoidTags;
	}
	
	public function escape($var)
	{
		if($var instanceof Doctrine_Collection) {
			foreach ($var as $k => $record) {
				$var[$k] = $this->escape($record);
			}
			return $var;
		}
		elseif($var instanceof Doctrine_Record) {
			foreach ($var as $k => $v) {
				$var->$k = $this->escape($v);
			}
			//We shouldn't be able to save the HTMLified version of this
			$var->lock();
			return $var;
		}
		elseif(is_array($var)) {
			foreach ($var as $k => $v) {
				$var[$k] = $this->escape($v);
			}
			return $var;
		}
		elseif(is_string($var)) {
			return $this->esc_callback($var);
		}
		else {
			return $var;
		}
	}
	
	private function esc_callback($value)
	{
		if(is_string($value)) {
			require_once HELPERS;
			return allhtmlentities($value, $this->avoid);
		}
		return $value;
	}
}
 
?>

<?php
/**
 */

class Kea_Request
{
	/**
	 * Holds the array of request data
	 * @var array $_SERVER['REQUEST'] data
	 */
	private $_properties = array();
	
	private $_request_method;
	
	private $_uri;
	
	/**
	 * Array to hold any feedback generated in the thread.
	 * @var array
	 */
	private $_feedback = array();
	
	/**
	 * Basic constructor
	 */
	public function __construct()
	{
		$this->_uri = $_SERVER['REQUEST_URI'];
		$this->_properties = $_REQUEST;
		$this->_request_method = $_SERVER['REQUEST_METHOD'];
	}
	
	/**
	 * Gets a property based off of a key from the `properties` array.
	 * @param string $key to `properties` array
	 * @return mixed or bool(false)
	 */
	public function getProperty( $key )
	{
		if( array_key_exists( $key, $this->_properties ) ) {
			return $this->_properties[$key];	
		}else{
			return false;
		}
	}
	
	/**
	 * Set a property in the `properties` array
	 * @param string $key is the key in the `properties` array
	 * @param mixed $val can be set to anything in the `properties` array
	 */
	public function setProperty( $key, $val )
	{
		$this->_properties[$key] = $val;
	}
	
	public function setProperties( $vals )
	{
		$this->_properties = $vals;
	}
	
	// This is essentially a recursive function that cleans up FORM-submitted data
	public function cleanParams( $array ) 
	{
		foreach($array  as  $k=>$v) {
			if (is_string($v)) $array[$k] = stripslashes($v);
			elseif (is_array($v)) {
				$array[$k] = $this->cleanParams($array[$k]);
			}
		}
		return $array;
	}

	public function getProperties()
	{
		return $this->cleanParams($this->_properties);
	}
	
	public function getParams()
	{
		return $this->_properties['params'];
	}
	
	public function getURI()
	{
		if( isset( $this->_uri ) ) {
			return $this->_uri;	
		}
		return false;
	}
	
	public function getRequestMethod()
	{
		return $this->_request_method;
	}
	
	/**
	 * Add a message to the `feedback` array
	 * @param string $msg is added to the `feedback` array
	 */
	public function addFeedback( $msg )
	{
		array_push( $this->_feedback, $msg );
	}
	
	/**
	 * Return the `feedback` array
	 * @return array
	 */
	public function getFeedback()
	{
		return $this->_feedback;
	}
	
	/**
	 * Return the `feedback` array as a string
	 * @param string $seperator acts as the seperator between feedback msgs
	 * @return string
	 */
	public function getFeedbackString( $seperator = "\n" )
	{
		return implode( $seperator, $this->_feedback );
	}
	
}

?>
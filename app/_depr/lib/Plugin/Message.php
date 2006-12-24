<?php
/**
 * undocumented class
 *
 * @package default
 * @author Kris Kelly
 **/
class Kea_Plugin_Message 
{
	private $data_object;
	private $data_array = array();
	private $controller_name;
	
	/**
	 * Methods array
	 *
	 * @var array key = method name, value = array of method arguments
	 **/
	private $method_array;
	private $model_class;
	private $method_result;
	private $new_result;
	
	public function __construct() {}
	
	public function isControllerMsg()
	{
		return !empty($this->controller_name);
	}
	
	public function isModelMsg()
	{
		return !empty($this->model_class);
	}
	
	public function getMethods()
	{
		return array_keys($this->method_array);
	}
	
	public function getController()
	{
		return $this->controller_name;
	}
	
	public function hasMethod( $name )
	{
		return array_key_exists($name, $this->method_array);
	}
	
	public function getMethodArgs( $method_name )
	{
		return $this->method_array[$method_name];
	}
	
	public function setController( $name )
	{
		if( is_string($name) ) $this->controller_name = $name;
	}
	
	public function addMethod( $name, $args = null)
	{
		$this->method_array[$name] = $args;
	}
	
	public function setResult( $result )
	{
		if( $result !== $this->method_result )
		{
			$this->method_result = $result;
			$this->new_result = TRUE;
		}
		else
		{
			$this->new_result = FALSE;
		}
		
	}
	
	public function getResult()
	{
		return $this->method_result;
	}
	
	public function resultChanged()
	{
		return $this->new_result;
	}
	
	public function removeMethod( $name )
	{
		unset($this->method_array[$name]);
	}
	
} // END class 
?>
<?php

abstract class Kea_Action_Controller extends Kea_Controller_Base
{	
	protected $before_filters = array();
	
	protected $after_filters = array();
	
	public $validationErrors = array();
	
	public function __call( $method, $args )
	{
		$method = '_' . $method;
		
		if( method_exists( $this, $method ) ) {
		
			$this->beforeFilter( $method, $this );

			$result = call_user_func_array( array( $this, $method ), $args );

		}
		if( $plugins = self::plugins() )
		{
			
			$method = ltrim($method, '_');
	
			$msg = new Kea_Plugin_Message();
			$msg->setController( get_class($this) );
			$msg->addMethod($method, $args);
			$msg->setResult(@$result);
			
			$msg = $plugins->notify( $msg );
			
			$newResult = $msg->getResult();
			
			$this->afterFilter( $newResult );
			
			return ($newResult) ? $newResult : @$result;
		}
		else
		{
			$this->afterFilter( $result );
			
			return $result;
		}

		throw new Kea_Action_Exception(
			'The method ' . $method . ' doesn\'t exist in the controller ' . get_class( $this ) . '.'
		);
	}
	
	protected function beforeFilter( &$method, &$args )
	{
		foreach( $this->before_filters as $filter ) {
			$filter->filter( $method, $args, $this );
		}
	}
	
	protected function afterFilter( &$result )
	{
		foreach( $this->after_filters as $filter ) {
			$filter->filter( $result, $this );
		}
	}
	
	protected function attachBeforeFilter( Kea_Filter $filter )
	{
		$this->before_filters[] = $filter;
	}

	protected function attachAfterFilter( Kea_Filter $filter )
	{
		$this->after_filters[] = $filter;
	}

	public function redirect( $redirect_to )
	{
		header( "Location: " . $redirect_to );
		//When you comment this out, uncaught exceptions are processed.  If this stays in, they are dropped
		//after all calls to redirect()
		//exit();
	}
	
	public function validates( Kea_Domain_Model $object )
	{
		if( $object->validates() ) {
			return true;
		}
		
		$namespace = get_class( $object );
		$errors = $object->getErrors();

		foreach( $errors as $property => $error ) {
			$this->validationErrors[$namespace][$property] = $error;
		}
		
		return false;
	}
	
	public function validationErrors()
	{
		return $this->validationErrors;
	}
	
	public function addError( $namespace, $property, $error )
	{
		$this->validationErrors[$namespace][$property] = $error;
	}
}
?>
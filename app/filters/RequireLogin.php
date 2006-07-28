<?php

class RequireLogin implements Kea_Filter
{
	private $protected_methods = array();
	
	private $redirect_to;
	
	public function __construct( $methods, $redirect_to = null )
	{
		if( is_array( $methods ) ) {
			foreach( $methods as $k => $v ) {
				$v = isset( $v ) ? $v : null;
				$this->protected_methods[$k] = $v;
			}
		} elseif( is_string( $methods ) ) {
				$this->protected_methods[$methods] = null;
		}
		
		$redirect_to ?
			$this->redirect_to = $redirect_to :
			$this->redirect_to = BASE_URI;
	}
	
	public function addMethod( $name )
	{
		if( !in_array( $name, $this->protected_methods ) ) {
			$this->protected_methods[] = $name;
		}
	}
	
	public function removeMethod( $name )
	{
		if( $key = array_search( $name, $this->protected_methods ) ) {
			unset( $this->protected_methods[$key] );
		}
	}
	
	public function filter( &$action, $controller )
	{
		if( array_key_exists( $action, $this->protected_methods ) )
		{
			$session = new Kea_Session;
			if( $user = $session->getUser() ) {
				if( $permissions = $this->protected_methods[$action] ) {
					if( $user->getPermissions() <= $permissions ) {
						return true;
					}
					$controller->redirect( $this->redirect_to );
				}
				return true;
			}
			$controller->redirect( $this->redirect_to );
		}
	}
}
?>
<?php

class Kea_Session
{	
	private static $_instance;
	private $_controlled = array(	'logged_in_user',
	 								'saved_location' );
	
	private function __construct() {}
	private function __clone() {}
	
	/**
	 * Classic singleton instantiator
	 * @return Kea_Session object
	 */
	public static function getInstance()
	{
		if (!self::$_instance instanceof self) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __destruct()
	{
		// Don't add session_write_close here for some unknown reason
		//session_write_close();
	}
	
	private function _init()
	{
		if( !isset( $_SESSION ) ) {
			session_start();
		}
		return $this;
	}
	
	protected function _get( $key )
	{
		$this->_init();
		if( isset( $_SESSION[__CLASS__][$key] ) ) {
			return $_SESSION[__CLASS__][$key];
		}
		return false;
	}
	
	protected function _set( $key, $val )
	{
		$this->_init();
		$_SESSION[__CLASS__][$key] = $val;
	}
	
	protected function _unset( $key )
	{
		$this->_init();
		if( isset( $_SESSION[__CLASS__][$key] ) )
		{
			unset( $_SESSION[__CLASS__][$key] );
			return true;
		}
		false;
	}
	
	public function setValue( $key, $val )
	{
		$this->_set( $key, $val );
	}
	
	public function getValue( $key )
	{
		if( in_array( $key, $this->_controlled ) ) {
			return false;
		}
		if( $val = $this->_get( $key ) ) {
			return $val;
		}
		return false;
	}
	
	public function unsetValue( $key )
	{
		if( in_array( $key, $this->_controlled ) )
		{
			return false;
		}
		elseif( $this->_unset( $key ) )
		{
			return true;
		}
		return false;
	}
	
	public function saveLocation()
	{
		$this->_set( 'saved_location', $_SERVER['REQUEST_URI'] );
		return true;
	}
	
	public function getSavedLocation()
	{
		return $this->_get( 'saved_location' );
	}
	
	public function loginUser( User $user )
	{
		if( $logged_in = $this->_get( 'logged_in_user' ) ) {
			throw new Kea_Session_Exception(
				'Currently logged in as: ' . $logged_in->getUsername()
			);
		} else {
			$this->_set( 'logged_in_user', $user );
			return true;
		}
	}
	
	public function logoutUser()
	{	
		if( $this->_get( 'logged_in_user' ) ) {
			unset( $_SESSION[__CLASS__]['logged_in_user'] );
		}
		$this->destroy();
		return true;
	}
	
	public function getUser()
	{
		return $this->_get( 'logged_in_user' );
	}
	
	public function isSuper()
	{
		if( $user = $this->getUser() )
		{
			if( $user->getPermissions() == 1 )
			{
				return true;
			}
		}
		return false;
	}
	
	public function isAdmin()
	{
		if( $user = $this->getUser() )
		{
			if( $user->getPermissions() <= 10 )
			{
				return true;
			}
		}
		return false;
	}
	
	public function isResearcher()
	{
		if( $user = $this->getUser() )
		{
			if( $user->getPermissions() <= 20 )
			{
				return true;
			}
		}
		return false;
	}
	
	public function isPrivResearcher()
	{
		if( $user = $this->getUser() )
		{
			if( $user->getPermissions() <= 15 )
			{
				return true;
			}
		}
		return false;
	}
	
	public function isPublic()
	{
		if( $user = $this->getUser() )
		{
			if( $user->getPermissions() <= 100 )
			{
				return true;
			}
		}
		return false;
	}
	
	public function destroy()
	{
		$_SESSION = array();
		if( isset( $_COOKIE[session_name()] ) ) {
			setcookie( session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}
	
	public function flash( $msg = null )
	{
		if( !$msg ) {
			$flash = $this->_get( 'flash' );
			$this->_set( 'flash', null );
			return $flash;
		} else {
			$this->_set( 'flash', $msg );
		}
	}
}

?>
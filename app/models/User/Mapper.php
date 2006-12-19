<?php
/**
 *
 * Copyright 2006:
 * George Mason University
 * Center for History and New Media,
 * State of Virginia 
 *
 * LICENSE
 *
 * This source file is subject to the GNU Public License that
 * is bundled with this package in the file GPL.txt, and the
 * specific license found in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL: 
 * http://www.gnu.org/licenses/gpl.txt
 * If you did not receive a copy of the GPL or local license and are unable to
 * obtain it through the world-wide-web, please send an email 
 * to chnm@gmu.edu so we can send you a copy immediately.
 *
 * This software is licensed under the GPL license by the Center
 * For History and New Media, at George Mason University, except 
 * where other free software licenses apply.
 * The source code may only be reused or redistributed if the
 * copyright notice and licensing information above are retained,
 * and other included Zend and Cake licenses, are preserved. 
 * 
 * @author Nate Agrin
 * @contributors Josh Greenburg, Kris Kelly, Dan Stillman
 * @license http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class User_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'users';
	protected $_unique_id	= 'user_id';
	
	public function __construct()
	{
		parent::__construct();
	}

	final public function login( $username = null, $password = null )
	{
		if( !$username || !$password ) {
			throw new Kea_DB_Mapper_Exception(
				'Please provide a username and password.'
			);
		} else {
			$stmt = $this->select()->where( 'user_username = ?', trim( $username ) )
						   		   ->where( 'user_password = SHA1( ? )', trim( $password ) )
						   		   ->where( 'user_active = ?', '1' );

			$result = $this->query( $stmt );

			if( $result->num_rows == 1 ) {
				return $this->load( $result );
			} else {
				throw new Kea_DB_Mapper_Exception(
					'This username and password don\'t match our records. Please try again.'
				);
			}
		}
	}
	
	public function delete( $id )
	{
		self::$_adapter->delete( $this->_table_name, 'user_id = "' . $id . '"' );
		return true;
	}
	
	public function doLoad( $array )
	{
		return new User( $array );
	}
	
	public function targetClass()
	{
		return 'User';
	}
	
	public function changePassword( $user_id, $old, $new )
	{
			
		// Superuser doesn't have to enter an old password
		if (!self::$_session->isSuper()):		
			$select = self::$_adapter->select();
			$select->from( 'users', 'user_id' )
					->where( 'user_id = ?', $user_id )
					->where( 'user_password = SHA1( ? )', $old );

			$result = self::$_adapter->fetchOne( $select );

			if( $result != $user_id ) {
				throw new Kea_DB_Mapper_Exception( 'Incorrect old password.' );
			}
		endif;
		
		$sql = "UPDATE users SET user_password = SHA1('$new') WHERE user_id = '$user_id'";
		if( self::$_adapter->query( $sql ) ) {
			return true;
		} else {
			throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		}
	}
}

// Cait is great
?>
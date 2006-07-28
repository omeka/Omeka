<?php

class AccountsController extends Kea_Action_Controller
{
	protected function _edit()
	{
		if( self::$_request->getProperty( 'change_password' ) ) {
			$old = self::$_request->getProperty( 'old_password' );
			$new1 = self::$_request->getProperty( 'new_password_1' );
			$new2 = self::$_request->getProperty( 'new_password_2' );

			if( empty( $new1 ) || empty( $new2 ) || empty( $old ) ) {
				throw new Kea_Exception( 'You must enter the information in all fields on the form.' );
			}

			if( $new1 !== $new2 ) {
				throw new Kea_Exception( 'The new passwords do not match.' );
			}
			
			$mapper = new User_Mapper;
			return $mapper->changePassword( self::$_session->getUser()->getId(), $old, $new1 );
		}
	}
	
	protected function _getMyContributions( $num_objects = 9, $order_by_date = true )
	{
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Object_Mapper();

		$select = $mapper->select( "*, RPAD( SUBSTRING( object_description, 1, 140 ),  LENGTH( SUBSTRING( object_description, 1, 140 ) ) + 3, '.') as short_desc" )
						->joinLeft( 'categories', 'categories.category_id = objects.category_id' )
						->where( 'objects.user_id = ?', self::$_session->getUser()->getId() );
		
		if( $order_by_date )
		{
			$select->order( array( 'objects.object_added' => 'DESC' ) );
		}
		else
		{
			$select->order( array( 'objects.object_id' => 'DESC' ) );
		}
		
		if( $tags = self::$_request->getProperty( 'tags' ) )
		{
			$select->joinLeft( 'objects_tags', 'objects_tags.object_id = objects.object_id' )
				   ->joinLeft( 'tags', 'objects_tags.tag_id = tags.tag_id' )
				   ->where( 'tags.tag_name = ?', $tags );
		}

		return $mapper->paginate( $select, $page, $num_objects );
	}
	
	protected function _getMyFavorites( $num_objects = 9, $order_by_date = true )
	{
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Object_Mapper();

		$select = $mapper->select( "*, RPAD( SUBSTRING( object_description, 1, 140 ),  LENGTH( SUBSTRING( object_description, 1, 140 ) ) + 3, '.') as short_desc" )
						->joinLeft( 'categories', 'categories.category_id = objects.category_id' )
						->join( 'objects_favorites', 'objects_favorites.object_id = objects.object_id' )
						->where( 'objects_favorites.user_id = ?', self::$_session->getUser()->getId() );
		
		if( $order_by_date )
		{
			$select->order( array( 'objects_favorites.fav_added' => 'DESC' ) );
		}
		else
		{
			$select->order( array( 'objects.object_id' => 'DESC' ) );
		}
		
		if( $tags = self::$_request->getProperty( 'tags' ) )
		{
			$select->join( 'objects_tags', 'objects_tags.object_id = objects.object_id' )
				   ->join( 'tags', 'objects_tags.tag_id = tags.tag_id' )
				   ->where( 'objects_tags.user_id = ?', self::$_session->getUser()->getId() )
				   ->where( 'tags.tag_name = ?', $tags );
		}

		return $mapper->paginate( $select, $page, $num_objects );
	}
	
	protected function _findMyTaggedObjects( $num_objects = 9 , $showAll = false)
	{
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Object_Mapper();

		$select = $mapper->select( "*, RPAD( SUBSTRING( object_description, 1, 140 ),  LENGTH( SUBSTRING( object_description, 1, 140 ) ) + 3, '.') as short_desc" )
						->joinLeft( 'categories', 'categories.category_id = objects.category_id' )
						->order( array( 'objects.object_id' => 'DESC' ) )
						->join( 'objects_tags', 'objects_tags.object_id = objects.object_id' )
					   	->join( 'tags', 'objects_tags.tag_id = tags.tag_id' )
				   		->where( 'objects_tags.user_id = ?', self::$_session->getUser()->getId() )
						->group('objects.object_id');
		
		if( $tags = self::$_request->getProperty( 'tags' ) ):
			$select->where( 'tags.tag_name = ?', $tags );
		endif;

		if( $tags || $showAll == true):
			return $mapper->paginate( $select, $page, $num_objects );
		else:
			return false;
		endif;
	
	}
	
}

?>
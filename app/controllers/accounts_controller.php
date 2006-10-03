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
				self::$_session->flash( 'You must enter the information in all fields on the form.' );
				return;
			}

			if( $new1 !== $new2 ) {
				self::$_session->flash( 'The new passwords do not match.' );
				return;
			}
			
			$mapper = new User_Mapper;
			return $mapper->changePassword( self::$_session->getUser()->getId(), $old, $new1 );
		}
	}
	
	protected function _getMyContributions( $num_items = 9, $order_by_date = true )
	{
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Item_Mapper();

		$select = $mapper->select( "*, RPAD( SUBSTRING( item_description, 1, 140 ),  LENGTH( SUBSTRING( item_description, 1, 140 ) ) + 3, '.') as short_desc" )
						->joinLeft( 'types', 'types.type_id = items.type_id' )
						->where( 'items.user_id = ?', self::$_session->getUser()->getId() );
		
		if( $order_by_date )
		{
			$select->order( array( 'items.item_added' => 'DESC' ) );
		}
		else
		{
			$select->order( array( 'items.item_id' => 'DESC' ) );
		}
		
		if( $tags = self::$_request->getProperty( 'tags' ) )
		{
			$select->joinLeft( 'items_tags', 'items_tags.item_id = items.item_id' )
				   ->joinLeft( 'tags', 'items_tags.tag_id = tags.tag_id' )
				   ->where( 'tags.tag_name = ?', $tags );
		}

		return $mapper->paginate( $select, $page, $num_items );
	}
	
	protected function _getMyFavorites( $num_items = 9, $order_by_date = true )
	{
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Item_Mapper();

		$select = $mapper->select( "*, RPAD( SUBSTRING( item_description, 1, 140 ),  LENGTH( SUBSTRING( item_description, 1, 140 ) ) + 3, '.') as short_desc" )
						->joinLeft( 'types', 'types.type_id = items.type_id' )
						->join( 'items_favorites', 'items_favorites.item_id = items.item_id' )
						->where( 'items_favorites.user_id = ?', self::$_session->getUser()->getId() );
		
		if( $order_by_date )
		{
			$select->order( array( 'items_favorites.fav_added' => 'DESC' ) );
		}
		else
		{
			$select->order( array( 'items.item_id' => 'DESC' ) );
		}
		
		if( $tags = self::$_request->getProperty( 'tags' ) )
		{
			$select->join( 'items_tags', 'items_tags.item_id = items.item_id' )
				   ->join( 'tags', 'items_tags.tag_id = tags.tag_id' )
				   ->where( 'items_tags.user_id = ?', self::$_session->getUser()->getId() )
				   ->where( 'tags.tag_name = ?', $tags );
		}

		return $mapper->paginate( $select, $page, $num_items );
	}
	
	protected function _findMyTaggedItems( $num_items = 9 , $showAll = false)
	{
		$page = isset( self::$_route['pass'][0] ) ? (int) self::$_route['pass'][0] : 1;

		$mapper = new Item_Mapper();

		$select = $mapper->select( "*, RPAD( SUBSTRING( item_description, 1, 140 ),  LENGTH( SUBSTRING( item_description, 1, 140 ) ) + 3, '.') as short_desc" )
						->joinLeft( 'types', 'types.type_id = items.type_id' )
						->order( array( 'items.item_id' => 'DESC' ) )
						->join( 'items_tags', 'items_tags.item_id = items.item_id' )
					   	->join( 'tags', 'items_tags.tag_id = tags.tag_id' )
				   		->where( 'items_tags.user_id = ?', self::$_session->getUser()->getId() )
						->group('items.item_id');
		
		if( $tags = self::$_request->getProperty( 'tags' ) ):
			$select->where( 'tags.tag_name = ?', $tags );
		endif;

		if( $tags || $showAll == true):
			return $mapper->paginate( $select, $page, $num_items );
		else:
			return false;
		endif;
	
	}
	
}

?>
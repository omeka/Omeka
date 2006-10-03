<?php

class TagsController extends Kea_Action_Controller
{
	protected function _deleteAssociation( $tag_id, $item_id )
	{
		return Tags::deleteAssociation( $tag_id, $item_id );
	}
	
	protected function _deleteMyTag( $tag_id, $item_id, $user_id )
	{
		return Tags::deleteMyTag( $tag_id, $item_id, $user_id );
	}
	
	protected function _addMyTags( $tag_string, $item_id, $user_id )
	{
		return Tags::addMyTags( $tag_string, $item_id, $user_id );
	}
	
	protected function _findByItem( $item_id )
	{
		$tags = new Tags();
		$tags->findByItem( $item_id );
		return $tags;
	}
	
	protected function _getTags( $num = 100 )
	{
		$tags = new Tags();
		return $tags->getTagsAndCount( $num , true, false );
	}
	
	protected function _getMaxCount()
	{
		$tags = new Tags();
		return $tags->getMaxCount();
	}
	
	protected function _findMyTags( $item_id = null )
	{
		$tags = new Tags();
		return $tags->getTagsAndCount( 100, false, true, $item_id, self::$_session->getUser()->getId() );
	}
}

?>
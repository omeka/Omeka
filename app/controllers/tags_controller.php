<?php

class TagsController extends Kea_Action_Controller
{
	protected function _deleteAssociation( $tag_id, $object_id )
	{
		return Tags::deleteAssociation( $tag_id, $object_id );
	}
	
	protected function _deleteMyTag( $tag_id, $object_id, $user_id )
	{
		return Tags::deleteMyTag( $tag_id, $object_id, $user_id );
	}
	
	protected function _addMyTags( $tag_string, $object_id, $user_id )
	{
		return Tags::addMyTags( $tag_string, $object_id, $user_id );
	}
	
	protected function _findByObject( $object_id )
	{
		$tags = new Tags();
		$tags->findByObject( $object_id );
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
	
	protected function _findMyTags( $object_id = null )
	{
		$tags = new Tags();
		return $tags->getTagsAndCount( 100, false, true, $object_id, self::$_session->getUser()->getId() );
	}
}

?>
<?php

class ContributorsController extends Kea_Action_Controller
{
	protected function _all( $type = 'object' , $sort = null)
	{
		$mapper = new Contributor_Mapper;
		switch( $type ) {
			case( 'object' ):
				if (@$sort == 'alpha') {
					$select = $mapper->find()
									->order( array( 'contributors.contributor_last_name' => 'ASC' ) );
					return $mapper->findObjects($select);
				}
				else {
					return $mapper->allObjects();
				}
			break;
			case( 'array' ):
			if (@$sort == 'alpha') {
				$select = $mapper->find()
								->order( array( 'contributors.contributor_last_name' => 'ASC' ) );
				return $mapper->findArray($select);
			}
			else {
				return $mapper->allArray();
			}
			break;
		}
		return false;
	}
	
	protected function _findById()
	{
		$id = self::$_request->getProperty( 'id' ) ?
				self::$_request->getProperty( 'id' ) : 
					(isset( self::$_route['pass'][0] ) ?
					self::$_route['pass'][0] :
					0);

		$mapper = new Contributor_Mapper();
		return $mapper->findById( $id );
	}
	
	protected function _edit()
	{
		if( self::$_request->getProperty( 'contributor_edit' ) )
		{
			$c = new Contributor( self::$_request->getProperty('contributor') );
			if( empty( $c->contributor_birth_year ) )
			{
				$c->contributor_birth_year = 'NULL';
			}
			if( $this->validates( $c ) )
			{
				$c->save();
				$this->redirect( BASE_URI . DS . 'contributors' . DS . 'all' . DS );
				return;
			}
			else
			{
				$c->contributor_birth_year = '';
				return $c;
			}
		}
		else
		{
			return $this->_findById();
		}
	}
	
	protected function _add()
	{
		if( self::$_request->getProperty( 'contributor_add' ) )
		{
			$c = new Contributor( self::$_request->getProperty('contributor') );
			if( empty( $c->contributor_birth_year ) )
			{
				$c->contributor_birth_year = 'NULL';
			}

			if( $this->validates( $c ) )
			{
				$c->save();
				$this->redirect( BASE_URI . DS . 'contributors' . DS . 'all' . DS );
				return;
			}
			else
			{
				$c->contributor_birth_year = '';
				return $c;
			}
		}
		else
		{
			return new Contributor;	
		}
	}
	
	protected function _delete()
	{
		if( self::$_request->getProperty( 'contributor_delete' ) )
		{
			$id = self::$_request->getProperty( 'contributor_id' );
			$mapper = new Contributor_Mapper;
			$mapper->delete( $id );
			$this->redirect( BASE_URI . DS . 'contributors' . DS . 'all' );
		}
	}
}

?>
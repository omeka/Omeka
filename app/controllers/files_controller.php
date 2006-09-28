<?php

class FilesController extends Kea_Action_Controller
{	
	protected function _total()
	{
		$mapper = new File_Mapper;
		return $mapper->total();
	}
	
	protected function _delete( $file_id )
	{
		return File::delete( $file_id );
	}
	
	protected function _findById( $id = null )
	{
		if( !$id )
		{
			$id = self::$_request->getProperty( 'id' ) ?
					self::$_request->getProperty( 'id' ) : 
						(isset( self::$_route['pass'][0] ) ?
						self::$_route['pass'][0] :
						0);	
		}
		
		$id = (int) $id;
		$mapper = new File_Mapper;
		return $mapper->find()
					  ->where( 'file_id = ?', $id )
					  ->execute();
	}
	
	protected function _edit()
	{
		if( !self::$_request->getProperty( 'file_edit' ) )
		{
			return $this->_findById();
		}
		
		$file = new File( self::$_request->getProperty( 'File' ) );
		if( $file->file_date )
		{
			
		}
		$this->scrubDate( $file->file_date );
		$this->scrubDate( $file->file_capture_date );
		
		$file->save();
		$this->redirect( BASE_URI . DS . 'files' . DS . 'show' . DS . $file->getId() );
	}
	
	private function scrubDate( &$date )
	{
		if( !empty( $date ) )
		{
			$date = date( 'Y-m-d H:i:s', strtotime( $date ) );
		}
		else
		{
			$date = 'NULL';
		}
	}
}

?>
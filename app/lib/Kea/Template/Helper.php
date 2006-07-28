<?php

class Kea_Template_Helper
{
	private $sub_helpers = array();
	private $sub_helpers_dir;
	
	public function __construct()
	{
		$this->sub_helpers_dir = dirname( __FILE__ ) . DS . 'Helpers';
	}
	
	public function getSubHelpers( $dir = null )
	{
		notemptyor( $dir, $this->sub_helpers_dir );
		$it = new DirectoryIterator( $dir );
		foreach( $it as $file ) {
			$filename = $file->getFileName();
			if( $filename != '.' && $filename != '..' && !$file->isDir() ) {
				$this->sub_helpers[] = substr( $filename, 0, strpos( $filename, '.php' ) );
			}
		}
		return $this->sub_helpers;
	}
	
	// Bug on case sensitive file systems
	public function loadSubHelper( $sub_helper )
	{
		$sub_helper = Kea_Base::formatName( $sub_helper );
		if( Kea_Base::loadFile( $sub_helper . '.php', $this->sub_helpers_dir, true ) ) {
			$class = ucfirst( $sub_helper ) . 'Helper';
			return new $class;
		}
	}
}

?>
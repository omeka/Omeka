<?php

class PartialHelper
{
	public function file( $file )
	{
		if( !strstr( $file, '.php' ) ) {
			$file .= '.php';
		}
		
		$abs_file = ABS_CONTENT_DIR . SELECTED_THEME_DIR . DS . 'partials' . DS . $file;
		
		if( file_exists( $abs_file ) ) {
			return $abs_file;
		}

		trigger_error( 'Cannot find partial: ' . $file );
	}
}

?>
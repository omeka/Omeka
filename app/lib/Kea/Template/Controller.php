<?php

/**
 */
class Kea_Template_Controller extends Kea_Controller_Base implements Kea_View_Interface
{	
	public $themes_dir;
	public $selected_theme_dir;
	public $sub_dir;
	
	public $params;
	
	public function __construct()
	{
		$this->themes_dir = ABS_CONTENT_DIR;
	}
	
	// Required by the interface
	public function createView( $route )
	{	
		if( isset( self::$_route['admin'] ) && self::$_route['admin'] == ADMIN_URI ) {

			$this->selected_theme_dir = ADMIN_THEME_DIR;
			define( 'SELECTED_THEME_DIR', ADMIN_THEME_DIR );
			define( 'BASE_URI', WEB_ROOT . DS . ADMIN_URI );

		} elseif ( defined( 'PUBLIC_THEME_DIR' ) ) {

			$this->selected_theme_dir = PUBLIC_THEME_DIR;
			define( 'SELECTED_THEME_DIR', PUBLIC_THEME_DIR );
			define( 'BASE_URI', WEB_ROOT );

		} else {
			throw new Kea_Template_Exception(
				'No theme is specified to load.'
			);
		}
		
		// Check if the template is set, if not set it to index
		$template = Kea_Base::formatName( !empty( self::$_route['template'] ) ?
											self::$_route['template'] :
											'index' );

		// Get the directory:
		// Well formed maps should eleminate
		// the need for formating the names
		$this->sub_dir = isset( self::$_route['directory'] ) ?
							DS . self::$_route['directory'] :
							null;
		
		// Does the template file exist?  If not 404 or die.
		$this->resolveTemplate( $template );

		$__c = Kea_Action_Master::instance();
		
		$helper = new Kea_Template_Helper;
		foreach( $helper->getSubHelpers() as $sub_helper ) {
			${'_' . $sub_helper} = $helper->loadSubHelper( $sub_helper );
		}

		$layout = false;

		if( !$layout ) {
			$this->findLayout( $template, $layout, $content_for_layout );
		}
		
		if( KEA_DEBUG_TEMPLATE ) {
			include( $template );
		} else {
			@include( $template );
		}
	}
	
	private function hasTemplateFile( $template, $dir = null )
	{
		notemptyor( $dir, $this->themes_dir . $this->selected_theme_dir . $this->sub_dir );
		try{
			$t_file = $template . '.php';
			if( $t_path = Kea_Base::loadFile( $t_file, $dir ) ) {
				return $t_path;
			}
			return false;
		} catch( Kea_Exception $e ) {
			$this->error = $e;
			return false;
		}
	}
	
	private function resolveTemplate( &$template )
	{
		if( $template = $this->hasTemplateFile( $template ) ) {
			return;
		} elseif( $template = $this->hasTemplateFile( '404' ) ) {
			return;
		} else {
			// Set this to try and load the global 404 page or die
			if( file_exists( GLOBAL_404 ) ) {
				include( GLOBAL_404 );
				exit();
			}
			throw new Kea_Template_Exception( 'Fatal error, no template to load.' );
		}
	}
	
	private function findLayout( &$template, &$layout, &$content_for_layout )
	{
		$handle = @fopen( $template, "r" );
		if( $handle ) {
			$i = 0;
			while( $i < 5 && !feof( $handle ) ) {
				if( preg_match( '/[lL]ayout:[\s]*([\w]+)[\s]*[;]/', fgets( $handle ), $match ) ) {
					$layout = $match[1];
					break;
				}
				$i++;
			}
			fclose($handle);
		}
	
		if( $layout && $layout_path = $this->hasTemplateFile(
				$layout, $this->themes_dir . $this->selected_theme_dir . '/layouts/' ) ) {
			$content_for_layout = $template;
			$template = $layout_path;
		}
	}

}

?>
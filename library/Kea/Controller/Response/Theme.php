<?php
/**
 * @created 10/13/06    
 * @edited 10/13/06
 */
require_once 'Kea/Controller/Response/Abstract.php';
class Kea_Controller_Response_Theme extends Kea_Controller_Response_Abstract
{
	private $_theme_dir;

	protected $_page;
	
	public function __construct()
	{
		$this->_theme_dir = KEA_THEME_DIR . DIRECTORY_SEPARATOR . KEA_THEME;
		$this->addHeader("Content-type", "text/html");
	}
	
	public function setPage($page)
	{
		$this->_page = $page;
	}
	
	public function getHeader($header="header", $echo=true)
	{
		if ($header_file = Kea::loadFile($this->_theme_dir, $header, false)) {
			ob_start();
			include $header_file;
			if ($echo) {
				echo ob_get_clean();
				return;
			} else {
				return ob_get_clean();
			}
		}
		return null;
	}
	
	public function getFooter($footer="footer", $echo=true)
	{
		if ($footer_file = Kea::loadFile($this->_theme_dir, $footer, false)) {
			ob_start();
			include $footer_file;
			if ($echo) {
				echo ob_get_clean();
				return;
			} else {
				return ob_get_clean();
			}
		}
		return null;
	}
	
	public function __toString()
	{
		if ($page_path = Kea::loadFile($this->_theme_dir, $this->_page, false)) {
			ob_start();
			include $page_path;
			$this->appendBody(ob_get_clean());
		}
		return parent::__toString();
	}
}

?>
<?php 
/**
* 
*/
class Omeka_View_Format_Xml extends Omeka_View_Format_Abstract
{
	protected function _render() {
		$this->setHeader('Content-Type', 'text/xml');
	}
}
 
?>

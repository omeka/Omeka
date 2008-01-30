<?php 
/**
* 
*/
class Omeka_View_Format_Json extends Omeka_View_Format_Abstract
{
	protected function _render()
	{
		//In the case of JSON, rendering the records returns the JSON array sans encoding
		$json_a = $this->renderRecords();
		
		require_once HELPERS;
		
		if($msg = flash(false)) {
			$json_a['Flash'] = $msg;
		}
		
		$json = Zend_Json::encode($json_a);
		
		//Prototype.js doesn't recognize JSON unless the header is X-JSON: {json} all on one line [KK]
		$config = Zend_Registry::get('config_ini');
		if (!(boolean) $config->debug->json) {
			$this->setHeader("X-JSON: $json");
			return $json;
		}else {
			//We could even render this with a nice screen (pass this back to the view object and render a script)
			return $json;
		}
	}
}
 
?>

<?php

require_once 'Kea/Controller/Action.php';
class SettingsController extends Kea_Controller_Action
{	
	public function indexAction() {
		$this->_forward('settings', 'edit');
	}
	
	public function editAction() {
		$table = Doctrine_Manager::getInstance()->getTable('Option');
				
		$defaults = array('site_title' => 'Omeka', 'copyright' => 'CHNM', 'meta_keywords' => 'meta, meta1', 'meta_author' => 'Meta Author', 'meta_description' => 'Meta Description');
		
		foreach( $defaults as $key => $value )
		{
			$$key = $table->findBySQL("name LIKE '$key'")->getFirst();
			if(!$$key) {
				$$key = new Option;
				$$key->name = $key;
				$$key->value = $value;
				$$key->save();
			}
		}
						
		//process the form
		if(!empty($_POST)) {
			foreach( $_POST as $key => $value )
			{
				if(isset($$key)) {
					$$key->value = get_magic_quotes_gpc() ? stripslashes( $value ) : $value;
					$$key->save();
				}
			}
		}
		
		$this->render('settings/edit.php', compact(array_keys($defaults)));
	}
}

?>
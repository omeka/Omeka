<?php

require_once 'Kea/Controller/Action.php';
class SettingsController extends Kea_Controller_Action
{	
	public function indexAction() {
		$this->_forward('settings', 'edit');
	}
	
	public function editAction() {
		$table = Doctrine_Manager::getInstance()->getTable('Option');
		
		//Any changes to this list should be reflected in the install script (and possibly the view functions)		
		$settings = array('site_title', 'copyright','meta_keywords', 'meta_author', 'meta_description');
		
		foreach( $settings as $setting )
		{
			$$setting = $table->findBySQL("name LIKE '$setting'")->getFirst();
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
		
		$this->render('settings/edit.php', compact($settings));
	}
}

?>